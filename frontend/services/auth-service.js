const AuthService = {

    async safeJson(response) {
        const text = await response.text();
        try { return JSON.parse(text); }
        catch (e) {
            console.error("NOT JSON RESPONSE:", text);
            throw new Error("Server returned non-JSON response");
        }
    },

    getToken() {
        return localStorage.getItem("token");
    },

    getUser() {
        let u = localStorage.getItem("user");
        return u ? JSON.parse(u) : null;
    },

    saveLogin(data) {
        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify(data));
        Script.updateNavbar();
    },

    init() {
        $("#loginForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 3
                }
            },
            messages: {
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please enter your password",
                    minlength: "Password must be at least 3 characters long"
                }
            },
            submitHandler: function (form) {
                const data = Object.fromEntries(new FormData(form).entries());
                AuthService.handleLogin(data.email, data.password);
                form.reset();
            }
        });

        $("#registerForm").validate({
            rules: {
                first_name: { required: true },
                last_name: { required: true },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 3,
                    maxlength: 50
                }
            },
            messages: {
                first_name: "Please enter your first name",
                last_name: "Please enter your last name",
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 3 characters long",
                    maxlength: "Password cannot be longer than 50 characters"
                }
            },
            submitHandler: function (form) {
                const data = Object.fromEntries(new FormData(form).entries());
                AuthService.handleRegister(data);
                form.reset();
            }
        });
    },

    handleLogin(email, password) {
        email = email.trim();
        password = password.trim();

        if (!email || !password) return toastr.error("All fields are required!");
        if (!email.includes("@")) return toastr.error("Invalid email format!");
        if (password.length < 3) return toastr.error("Password must be at least 3 characters!");

        $("#loginBtn").prop("disabled", true);

        $.blockUI({ message: '<h3>Processing...</h3>' });

        fetch(API + "/auth/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        })
        .then(r => this.safeJson(r))
        .then(res => {
            $.unblockUI();
            $("#loginBtn").prop("disabled", false);

            if (!res.success) return toastr.error(res.error || "Login failed");

            this.saveLogin(res.data);
            toastr.success("Logged in successfully!");
            $("#loginModal").modal("hide");
            window.location.hash = "#vote";
        })
        .catch(() => {
            $.unblockUI();
            $("#loginBtn").prop("disabled", false);
            toastr.error("Server error, try again.");
        });
    },

    handleRegister(data) {
        data.first_name = data.first_name.trim();
        data.last_name = data.last_name.trim();
        data.email = data.email.trim();
        data.password = data.password.trim();

        if (!data.first_name || !data.last_name || !data.email || !data.password)
            return toastr.error("Please fill out all fields");

        if (!data.email.includes("@"))
            return toastr.error("Invalid email format");

        if (data.password.length < 3)
            return toastr.error("Password must be at least 3 characters!");

        $("#registerBtn").prop("disabled", true);

        $.blockUI({ message: '<h3>Creating account...</h3>' });

        fetch(API + "/auth/register", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(r => this.safeJson(r))
        .then(res => {
            $.unblockUI();
            $("#registerBtn").prop("disabled", false);

            if (!res.success) return toastr.error(res.error || "Registration failed");

            toastr.success("Account created! Logging you in...");
            $("#registerModal").modal("hide");
            this.handleLogin(data.email, data.password);
        })
        .catch(() => {
            $.unblockUI();
            $("#registerBtn").prop("disabled", false);
            toastr.error("Server error, try again.");
        });
    },


    logout() {
        localStorage.clear();
        Script.updateNavbar();
        window.location.hash = "#home";
        if (window.toastr) toastr.info("Logged out");
    }
};