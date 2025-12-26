const AdminPanelService = {

    renderPanel() {
        if ($("#adminPanel").length) return;

        $("#adminContainer").html(`
            <div id="adminPanel" class="admin-only bg-light p-3 border rounded"
            style="position:fixed;bottom:20px;right:20px;width:300px;z-index:9999">
                <h5 class="fw-bold mb-3">Admin Panel</h5>

                <button class="btn btn-primary w-100 mb-2" id="btnCreateElection">Create Election</button>
                <button class="btn btn-primary w-100 mb-2" id="btnCreateCandidate">Create Candidate</button>
                <button class="btn btn-primary w-100" id="btnAssignCandidate">Assign Candidate</button>
                <hr>
                <button class="btn btn-danger w-100 mb-2" id="btnDeleteElection">Delete Election</button>
                <button class="btn btn-danger w-100" id="btnDeleteCandidate">Delete Candidate</button>
            </div>
        `);

        $("#btnCreateElection").click(() => this.createElection());
        $("#btnCreateCandidate").click(() => this.createCandidate());
        $("#btnAssignCandidate").click(() => this.assignCandidate());
        $("#btnDeleteElection").click(() => this.deleteElection());
        $("#btnDeleteCandidate").click(() => this.deleteCandidate());
    },


    async createElection() {
        const title = prompt("Election Title:");
        const start = prompt("Start date (YYYY-MM-DD HH:MM:SS)");
        const end = prompt("End date (YYYY-MM-DD HH:MM:SS)");
        if (!title || !start || !end) return;

        const res = await fetch(API + "/elections", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: "Bearer " + AuthService.getToken()
            },
            body: JSON.stringify({ title, start_date: start, end_date: end })
        });

        const out = await AuthService.safeJson(res);
        alert(out?.message || "Election created!");
    },


    async createCandidate() {
        const fn = prompt("First Name:");
        const ln = prompt("Last Name:");
        const party = prompt("Party:");
        if (!fn) return;

        const res = await fetch(API + "/candidates", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: "Bearer " + AuthService.getToken()
            },
            body: JSON.stringify({ first_name: fn, last_name: ln, party })
        });

        const out = await AuthService.safeJson(res);
        alert(out?.message || "Candidate created!");
    },


    async assignCandidate() {
        const e = prompt("Election ID:");
        const c = prompt("Candidate ID:");
        if (!e || !c) return;

        const res = await fetch(API + `/elections/${e}/candidates/${c}`, {
            method: "POST",
            headers: { Authorization: "Bearer " + AuthService.getToken() }
        });

        const out = await AuthService.safeJson(res);
        alert(out?.message || "Candidate assigned!");
    },


    async deleteElection() {
        const id = prompt("Election ID:");
        if (!id) return;

        const res = await fetch(API + `/elections/${id}`, {
            method: "DELETE",
            headers: { Authorization: "Bearer " + AuthService.getToken() }
        });

        const out = await AuthService.safeJson(res);
        alert(out?.message || "Election deleted!");
    },


    async deleteCandidate() {
        const id = prompt("Candidate ID:");
        if (!id) return;

        const res = await fetch(API + `/candidates/${id}`, {
            method: "DELETE",
            headers: { Authorization: "Bearer " + AuthService.getToken() }
        });

        const out = await AuthService.safeJson(res);
        alert(out?.message || "Candidate deleted!");
    }
};