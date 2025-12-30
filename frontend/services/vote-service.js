const VoteService = {

    async loadVotePage() {
        const container = $("#voteContainer");
        const user = AuthService.getUser();

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
            headers: { Authorization: "Bearer " + AuthService.getToken() }
        });

        const elections = await AuthService.safeJson(res);

        if (!elections?.length) {
            $("#candidateBox").html("<p>No elections available.</p>");
            return;
        }

        const select = $("#electionSelect");
        elections.forEach(e => {
            select.append(`<option value="${e.id}">${e.title}</option>`);
        });

        this.loadCandidatesForElection(elections[0].id);

        select.on("change", (e) => {
            this.loadCandidatesForElection(e.target.value);
        });
    },



    async loadCandidatesForElection(electionId) {
        const box = $("#candidateBox");
        box.html("<p>Loading candidates...</p>");

        const res = await fetch(API + `/elections/${electionId}/candidates`, {
            headers: { Authorization: "Bearer " + AuthService.getToken() }
        });

        const candidates = await AuthService.safeJson(res);

        if (!candidates?.length) {
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

        $("#voteForm").on("submit", async (e) => {
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
                    Authorization: "Bearer " + AuthService.getToken()
                },
                body: JSON.stringify({
                    voter_id: AuthService.getUser().id,
                    election_id: electionId,
                    candidate_id: candidateId
                })
            });

            const out = await AuthService.safeJson(voteRes);

            if (!voteRes.ok) {
                alert(out.message);
                return;
            }

            alert("Vote submitted successfully!");
            ResultService.loadResults();
        });
    }
};