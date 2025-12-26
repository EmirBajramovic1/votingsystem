var app = $.spapp({
  defaultView: "#home",
  templateDir: "./"
});

function get_api_base_url() {
    if (location.hostname === "localhost" || location.hostname === "127.0.0.1") {
        return "http://localhost/projects/votingsystem/backend/rest";
    } else {
        return "https://digitalocean/rest"; 
    }
}

const API = get_api_base_url();

app.route({
    view: "vote",
    onCreate: () => {
        VoteService.loadVotePage();
    }
});

app.route({
    view: "results",
    onCreate: () => {
        ResultService.loadResults();
    }
});

app.run();

const Script = {
    updateNavbar() {
        const user = AuthService.getUser();

        if (!user) {
            $("#navAuth").show();
            $("#navUser").hide();
            $(".admin-only").hide();
            return;
        }

        $("#navUsername").text(`${user.first_name} ${user.last_name}`);
        $("#navAuth").hide();
        $("#navUser").show();

        if (user.role === "admin") {
            $(".admin-only").show();
            AdminPanelService.renderPanel();
        } else {
            $(".admin-only").hide();
        }
    }
};

$(document).ready(() => {
    Script.updateNavbar();
    AuthService.init();
    $("#logoutBtn").on("click", AuthService.logout);
});