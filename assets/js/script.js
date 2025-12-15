var app = $.spapp({
  defaultView: "#home",
  templateDir: "./"
});

app.run();

app.route({
  view: "vote",
  onCreate: function () {
    loadVotePage();
  }
});

app.route({
  view: "results",
  onCreate: function () {
    loadResults();
  }
});

const API = "http://localhost/projects/votingsystem/backend/rest";

async function safeJson(response) {
    const text = await response.text();
    try {
        return JSON.parse(text);
    } catch (e) {
        console.error("NOT JSON RESPONSE:", text);
        throw new Error("Server returned non-JSON response");
    }
}

function getToken() {
    return localStorage.getItem("token");
}

function getUser() {
    let u = localStorage.getItem("user");
    return u ? JSON.parse(u) : null;
}

function saveLogin(result) {
    localStorage.removeItem("token");
    localStorage.removeItem("user");

    localStorage.setItem("token", result.data.token);

    const user = {
        id: result.data.id,
        first_name: result.data.first_name,
        last_name: result.data.last_name,
        email: result.data.email,
        role: result.data.role
    };

    localStorage.setItem("user", JSON.stringify(user));

    updateNavbar();
}


function logout() {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    updateNavbar();
    window.location.hash = "#home";
}

function updateNavbar() {
    const user = getUser();

    if (!user) {
        $("#navAuth").show();
        $("#navUser").hide();
        $(".admin-only").hide();
        return;
    }

    $("#navUsername").text(user.first_name + " " + user.last_name);
    $("#navAuth").hide();
    $("#navUser").show();

    if (user.role === "admin") {
        $(".admin-only").show();
        injectAdminPanel();
    } else {
        $(".admin-only").hide();
    }
}

$("#loginBtn").on("click", async function () {
    let data = {
        email: $("#loginEmail").val(),
        password: $("#loginPassword").val()
    };

    let response;
    let result;

    try {
        response = await fetch(API + "/auth/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        result = await safeJson(response);

    } catch (err) {
        alert("Server error. Try again.");
        return;
    }

    if (!response.ok || result.success === false) {
        alert(result.message || "Invalid email or password");
        return;
    }

    saveLogin(result);
    alert("Logged in!");
    window.location.hash = "#vote";
});


$("#registerBtn").on("click", async function () {
    const email = $("#regEmail").val();
    const password = $("#regPassword").val();

    let registerRes = await fetch(API + "/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            first_name: $("#regFirst").val(),
            last_name: $("#regLast").val(),
            email,
            password
        })
    });

    let registerResult = await registerRes.json();

    if (!registerRes.ok || registerResult.success === false) {
        alert(registerResult.error || registerResult.message || "Registration failed");
        return;
    }

    localStorage.clear();

    let loginRes = await fetch(API + "/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
    });

    let loginResult = await loginRes.json();

    if (!loginRes.ok) {
        alert("Registered, but login failed");
        return;
    }

    saveLogin(loginResult);

    alert("Registered & logged in!");
    window.location.hash = "#home";
});

async function loadVotePage() {
    const container = $("#voteContainer");
    const user = getUser();

    if (!user) {
        container.html(`
            <div class="text-center">
                <h3>Please log in to vote.</h3>
            </div>
        `);
        return;
    }

    container.html(`
        <div class="text-center mb-4">
            <h1 class="fw-bold">Cast Your Vote</h1>
            <p class="text-secondary fs-5">
                Your voice matters. Make your choice for the election.
            </p>
        </div>

        <div class="card shadow-sm p-4">
            <h4 class="fw-bold mb-3">Select Election</h4>
            <select id="electionSelect" class="form-select mb-4"></select>
            <div id="candidateBox"></div>
        </div>
    `);

    const res = await fetch(API + "/elections", {
        headers: { Authorization: "Bearer " + getToken() }
    });

    const elections = await safeJson(res);

    if (!elections.length) {
        $("#candidateBox").html("<p>No elections available.</p>");
        return;
    }

    const select = $("#electionSelect");
    elections.forEach(e => {
        select.append(`<option value="${e.id}">${e.title}</option>`);
    });

    loadCandidatesForElection(elections[0].id);

    select.on("change", function () {
        loadCandidatesForElection(this.value);
    });
}

async function loadCandidatesForElection(electionId) {
    const box = $("#candidateBox");
    box.html("<p>Loading candidates...</p>");

    const res = await fetch(API + `/elections/${electionId}/candidates`, {
        headers: { Authorization: "Bearer " + getToken() }
    });

    const candidates = await safeJson(res);

    if (!candidates.length) {
        box.html("<p>No candidates assigned.</p>");
        return;
    }

    let html = `<form id="voteForm">`;

    candidates.forEach(c => {
        html += `
            <div class="form-check card flex-row align-items-center p-3 mb-3">

                <input 
                    class="form-check-input mx-3 big-radio"
                    type="radio"
                    name="candidate"
                    value="${c.id}"
                >

                <label class="form-check-label m-0 w-100">
                    <p class="fw-bold fs-5 mb-0">
                        ${c.first_name} ${c.last_name}
                    </p>
                    <p class="text-secondary mb-0">
                        ${c.party}
                    </p>
                </label>

            </div>
        `;
    });


    html += `
        <button class="btn btn-success w-100 mt-4 p-3 fs-5 fw-bold">
            Submit Vote
        </button>
    </form>
    `;

    box.html(html);

    $("#voteForm").on("submit", async function (e) {
        e.preventDefault();

        const candidateId = $("input[name='candidate']:checked").val();
        if (!candidateId) {
            alert("Please select a candidate.");
            return;
        }

        const voteRes = await fetch(API + "/votes", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: "Bearer " + getToken()
            },
            body: JSON.stringify({
                voter_id: getUser().id,
                election_id: electionId,
                candidate_id: candidateId
            })
        });

        const out = await safeJson(voteRes);

        if (!voteRes.ok) {
            alert(out.message);
            return;
        }

        alert("Vote submitted successfully!");
        loadResults();
    });
}

async function loadResults() {
    const container = $("#results");

    container.html(`
        <div class="container py-4">
            <div class="text-center mb-4">
                <h1 class="fw-bold">Election Results</h1>
                <p class="text-secondary mb-0 fs-5">Live voting results</p>
            </div>

            <div class="card shadow-sm p-4">
                <h4 class="fw-bold mb-2">Select Election</h4>
                <select id="resultsElectionSelect" class="form-select mb-4"></select>
                <div id="resultsBox"></div>
            </div>
        </div>
    `);

    try {
        const electionsRes = await fetch(API + "/elections", {
            headers: { "Authorization": "Bearer " + getToken() }
        });

        if (!electionsRes.ok) throw new Error("Failed to load elections");

        const elections = await electionsRes.json();
        if (!elections.length) {
            $("#resultsBox").html("<p>No elections found.</p>");
            return;
        }

        const select = $("#resultsElectionSelect");
        select.empty();

        elections.forEach(e => {
            select.append(`<option value="${e.id}">${e.title}</option>`);
        });

        loadElectionResults(elections[0].id);

        select.on("change", function () {
            loadElectionResults(this.value);
        });

    } catch (err) {
        console.error(err);
        container.html("<p>Error loading elections.</p>");
    }
}

async function loadElectionResults(electionId) {
    const box = $("#resultsBox");
    box.html("<p>Loading results...</p>");

    try {
        const res = await fetch(
            API + `/elections/${electionId}/results`,
            { headers: { "Authorization": "Bearer " + getToken() } }
        );

        if (!res.ok) throw new Error("Failed to load results");

        const results = await res.json();
        if (!results.length) {
            box.html("<p>No votes yet.</p>");
            return;
        }

        let html = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Votes</th>
                    </tr>
                </thead>
                <tbody>
        `;

        results.forEach(r => {
            html += `
                <tr>
                    <td>${r.first_name} ${r.last_name}</td>
                    <td>${r.votes_received}</td>
                </tr>
            `;
        });

        html += "</tbody></table>";
        box.html(html);

    } catch (err) {
        console.error(err);
        box.html("<p>Error loading results.</p>");
    }
}

function loadHomePage() {
    $("#home").load("views/home.html");
}

function injectAdminPanel() {
    if ($("#adminPanel").length > 0) return;

    $("#adminContainer").html(`
        <div id="adminPanel" class="admin-only bg-light p-4 border"
             style="position: fixed; bottom: 20px; right: 20px; width: 320px; z-index: 99999;">
            <h4 class="fw-bold mb-3">Admin Tools</h4>

            <button class="btn btn-primary w-100 mb-2" id="btnCreateElection">
                Create Election
            </button>

            <button class="btn btn-primary w-100 mb-2" id="btnCreateCandidate">
                Create Candidate
            </button>

            <button class="btn btn-primary w-100 mb-2" id="btnAssignCandidate">
                Assign Candidate
            </button>

            <hr>

            <button class="btn btn-danger w-100 mb-2" id="btnDeleteElection">
                Delete Election
            </button>

            <button class="btn btn-danger w-100" id="btnDeleteCandidate">
                Delete Candidate
            </button>
        </div>
    `);

    $("#btnCreateElection").on("click", createElection);
    $("#btnCreateCandidate").on("click", createCandidate);
    $("#btnAssignCandidate").on("click", assignCandidate);
    $("#btnDeleteElection").on("click", deleteElection);
    $("#btnDeleteCandidate").on("click", deleteCandidate);
}


async function createElection() {
    let title = prompt("Election Title:");
    let start = prompt("Start Date (YYYY-MM-DD HH:MM:SS):");
    let end = prompt("End Date (YYYY-MM-DD HH:MM:SS):");

    if (!title || !start || !end) return;

    let res = await fetch(API + "/elections", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + getToken()
        },
        body: JSON.stringify({ title, start_date: start, end_date: end })
    });

    let text = await res.text();
    let out;

    try {
        out = JSON.parse(text);
    } catch {
        alert("Server error creating election");
        console.error(text);
        return;
    }

    if (!res.ok) {
        alert(out.message);
        return;
    }

    alert("Election created!");
    loadVotePage();
}


async function createCandidate() {
    let fn = prompt("First Name:");
    let ln = prompt("Last Name:");
    let party = prompt("Party:");
    if (!fn || !ln || !party) return;

    await fetch(API + "/candidates", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + getToken()
        },
        body: JSON.stringify({ first_name: fn, last_name: ln, party })
    });

    alert("Candidate created!");
}

async function assignCandidate() {
    let electionId = prompt("Election ID:");
    let candidateId = prompt("Candidate ID:");
    if (!electionId || !candidateId) return;

    await fetch(API + `/elections/${electionId}/candidates/${candidateId}`, {
        method: "POST",
        headers: { "Authorization": "Bearer " + getToken() }
    });

    alert("Candidate assigned!");
}

async function deleteElection() {
    const id = prompt("Enter Election ID to delete:");
    if (!id) return;

    if (!confirm("Are you sure you want to delete this election?")) return;

    const res = await fetch(API + `/elections/${id}`, {
        method: "DELETE",
        headers: {
            Authorization: "Bearer " + getToken()
        }
    });

    const out = await safeJson(res);

    if (!res.ok) {
        alert(out.message || "Delete failed");
        return;
    }

    alert("Election deleted!");
    loadVotePage();
    loadResults();
}

async function deleteCandidate() {
    const id = prompt("Enter Candidate ID to delete:");
    if (!id) return;

    if (!confirm("Are you sure you want to delete this candidate?")) return;

    const res = await fetch(API + `/candidates/${id}`, {
        method: "DELETE",
        headers: {
            Authorization: "Bearer " + getToken()
        }
    });

    const out = await safeJson(res);

    if (!res.ok) {
        alert(out.message || "Delete failed");
        return;
    }

    alert("Candidate deleted!");
}

$(document).ready(updateNavbar);
$("#logoutBtn").on("click", logout);