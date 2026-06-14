<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Firebase Login Test</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', system-ui, Arial;
    }

    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: radial-gradient(circle at top, #1a1a2e, #0f0f1a);
      color: white;
    }

    .card {
      width: 420px;
      padding: 30px;
      border-radius: 18px;

      background: rgba(255, 255, 255, 0.06);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);

      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    h2 {
      margin: 0 0 20px;
      font-size: 22px;
      text-align: center;
      letter-spacing: 0.5px;
    }

    .input {
      width: 100%;
      padding: 12px 14px;
      margin: 10px 0;
      border-radius: 10px;

      border: 1px solid rgba(255, 255, 255, 0.15);
      background: rgba(0, 0, 0, 0.2);
      color: white;

      outline: none;
      transition: 0.3s;
    }

    .input:focus {
      border-color: #6c5ce7;
      box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
    }

    .btn {
      width: 100%;
      padding: 12px;
      margin-top: 10px;

      border: none;
      border-radius: 10px;

      cursor: pointer;
      font-weight: bold;
      letter-spacing: 0.5px;

      transition: 0.3s;
    }

    .btn-login {
      background: linear-gradient(135deg, #6c5ce7, #a29bfe);
      color: white;
    }

    .btn-login:hover {
      transform: translateY(-2px);
    }

    .btn-api {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      margin-top: 8px;
    }

    .btn-api:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    pre {
      margin-top: 15px;
      padding: 12px;
      border-radius: 10px;

      background: rgba(0, 0, 0, 0.5);
      color: #00ff9d;

      font-size: 12px;
      max-height: 180px;
      overflow: auto;
    }

    .title {
      text-align: center;
      margin-bottom: 15px;
      font-size: 18px;
      opacity: 0.9;
    }

    .dot {
      height: 8px;
      width: 8px;
      background: #6c5ce7;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
  </style>
</head>

<body>

  <div class="card">
    <div class="title">
      <span class="dot"></span>
      Firebase Auth Test Panel
    </div>

    <h2>Login</h2>

    <input class="input" type="email" id="email" placeholder="Email">
    <input class="input" type="password" id="password" placeholder="Password">

    <button class="btn btn-login" onclick="login()">Sign In</button>
    <button class="btn btn-api" onclick="getMe()">Call /me API</button>

    <pre id="output">Waiting for action...</pre>
  </div>

  <script>
    let token = null;

    async function login() {
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      const apiKey = "AIzaSyCNcZviXtFN8-H2VQD-X3xCUJ9FoVAwPfU";

      const res = await fetch(
        `https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=${apiKey}`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            email,
            password,
            returnSecureToken: true
          })
        }
      );

      const data = await res.json();

      document.getElementById('output').innerText =
        JSON.stringify(data, null, 2);

      if (data.idToken) {
        token = data.idToken;
        alert("Login success ✅");
      }
    }

    async function getMe() {
      if (!token) {
        alert("Please login first");
        return;
      }

      const res = await fetch("/api/me", {
        method: "GET",
        headers: {
          "Authorization": "Bearer " + token,
          "Accept": "application/json"
        }
      });

      const data = await res.json();

      document.getElementById('output').innerText =
        JSON.stringify(data, null, 2);
    }
  </script>

</body>

</html>
