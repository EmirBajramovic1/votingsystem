const ResultService = {

    async loadResults() {
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
                headers: { "Authorization": "Bearer " + AuthService.getToken() }
            });

            if (!electionsRes.ok) throw new Error("Failed to load elections");

            const elections = await AuthService.safeJson(electionsRes);
            if (!elections.length) {
                $("#resultsBox").html("<p>No elections found.</p>");
                return;
            }

            const select = $("#resultsElectionSelect");
            select.empty();

            elections.forEach(e => {
                select.append(`<option value="${e.id}">${e.title}</option>`);
            });

            ResultService.loadElectionResults(elections[0].id);

            select.on("change", function () {
                ResultService.loadElectionResults(this.value);
            });

        } catch (err) {
            console.error(err);
            container.html("<p>Error loading elections.</p>");
        }
    },

    async loadElectionResults(electionId) {
        const box = $("#resultsBox");
        box.html("<p>Loading results...</p>");

        try {
            const res = await fetch(
                API + `/elections/${electionId}/results`,
                { headers: { "Authorization": "Bearer " + AuthService.getToken() } }
            );

            if (!res.ok) throw new Error("Failed to load results");

            const results = await AuthService.safeJson(res);
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
};