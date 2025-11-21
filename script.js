 import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInWithEmailAndPassword, signInWithCustomToken, signInAnonymously, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        
        // --- 1. CONFIGURATION AND INITIALIZATION ---
        
        // Global variables provided by the environment
        const firebaseConfig = JSON.parse(typeof __firebase_config !== 'undefined' ? __firebase_config : '{}');
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        
        // DOM Elements
        const loginForm = document.getElementById('login-form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const errorMessageDiv = document.getElementById('error-message');
        const errorTextSpan = document.getElementById('error-text');
        const createAccountLink = document.getElementById('create-account-link');

        // Since we can't redirect to another page easily in this single-file environment,
        // we'll update the 'create account' link to show an instructional message.
        createAccountLink.addEventListener('click', (e) => {
            e.preventDefault();
            showMessage("To create an account, you would typically be directed to a separate registration page, e.g., `/COMMERCE/create.php`.", "bg-yellow-100 border-yellow-400 text-yellow-700");
        });

        // Function to display messages (error or success)
        const showMessage = (message, className = "bg-red-100 border-red-400 text-red-700") => {
            errorTextSpan.textContent = message;
            errorMessageDiv.className = `${className} px-4 py-3 rounded-lg relative mb-4`;
            errorMessageDiv.classList.remove('hidden');
        };

        const hideMessage = () => {
            errorMessageDiv.classList.add('hidden');
            errorTextSpan.textContent = '';
        };

        // --- 2. FIREBASE AUTHENTICATION SETUP ---

        const authenticate = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
                    await signInWithCustomToken(auth, __initial_auth_token);
                } else {
                    await signInAnonymously(auth);
                }
                console.log(`Firebase initialized. Current user: ${auth.currentUser?.uid || 'Anonymous'}`);
            } catch (error) {
                console.error("Firebase authentication error:", error);
            }
        };

        // Run initial authentication
        authenticate();

        // Check if the user is already logged in (redirect logic equivalent)
        onAuthStateChanged(auth, (user) => {
            if (user && !user.isAnonymous) {
                // If a non-anonymous user is found (meaning successful login occurred elsewhere or persisted)
                // In a real app, this would redirect. Here, we notify the user.
                console.log("User already logged in:", user.email);
                showMessage(`Welcome back, ${user.email}! You are logged in.`, "bg-green-100 border-green-400 text-green-700");
            }
        });


        // --- 3. LOGIN SUBMISSION HANDLER ---
        
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideMessage();

            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!email || !password) {
                showMessage("Email and Password fields are required.");
                return;
            }

            try {
                // Firebase function to sign in with email and password
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                const user = userCredential.user;

                // Success Message (simulating redirect to /COMMERCE/index.html)
                showMessage(`Login successful! Redirecting to home page for user: ${user.email}`, "bg-green-100 border-green-400 text-green-700");
                
                // In a real application, you would perform the redirection here:
                // window.location.href = "/COMMERCE/index.html";
                
            } catch (error) {
                let message = "An unknown error occurred during login.";
                console.error("Login Error:", error.code, error.message);

                // Handle common Firebase Auth error codes
                switch (error.code) {
                    case 'auth/user-not-found':
                    case 'auth/wrong-password':
                    case 'auth/invalid-credential':
                        message = "Invalid Email or Password. Please try again.";
                        break;
                    case 'auth/invalid-email':
                        message = "The email address format is invalid.";
                        break;
                    case 'auth/user-disabled':
                        message = "This user account has been disabled.";
                        break;
                    default:
                        message = "Login failed. Check your credentials.";
                }
                
                showMessage(message);
            }
        });