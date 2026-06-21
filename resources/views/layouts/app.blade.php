<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>

    <style>
        * {
    box-sizing: border-box;
}

html {
    font-size: 14px; /* reduces overall scale */
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f7f7fb;
    color: #1f2937;
    line-height: 1.5;
}

/* NAVBAR */
nav {
    background: #4f46e5;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

/* BRAND */
nav div {
    color: white;
    font-weight: bold;
    font-size: 16px;
}

/* LINKS */
.nav-links {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* NAV LINKS */
nav a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

nav a:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* CONTAINER */
.container {
    width: min(92%, 800px);
    margin: 20px auto;
    background: #ffffff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}

/* BUTTON */
.btn {
    background: #4f46e5;
    color: white;
    padding: 10px 14px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
}

.btn:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

/* ALERT */
.alert {
    padding: 12px;
    margin-bottom: 15px;
    background: #ecfdf5;
    color: #065f46;
    border-left: 5px solid #10b981;
    border-radius: 6px;
}

/* FORM ELEMENTS */
input, select, textarea {
    border: 1px solid #d1d5db;
    padding: 10px;
    border-radius: 6px;
    width: 100%;
    font-size: 14px;
}

/* ===== MOBILE (phones) ===== */
@media (max-width: 768px) {

      html {
        font-size: 13px;
    }

    nav {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }

    .nav-links {
        width: 100%;
        flex-direction: column;
    }

    nav a {
        width: 100%;
        text-align: center;
        padding: 10px;
        font-size: 14px;
    }

    .container {
        width: 95%;
        margin: 12px auto;
        padding: 14px;
        border-radius: 10px;
    }

    .btn {
        width: 100%;
        font-size: 14px;
    }
}

/* ===== SMALL PHONES ===== */
@media (max-width: 480px) {

    nav div {
        font-size: 16px;
    }

    .container {
        padding: 15px;
    }

    nav a {
        font-size: 14px;
        padding: 10px;
    }
}
    </style>
</head>

<body>

<nav>
    <div style="color:white; font-weight:bold;">{{ config('app.name') }}</div>

    <div class="nav-links">
        <a href="/">Home</a>
       
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

</body>
</html>