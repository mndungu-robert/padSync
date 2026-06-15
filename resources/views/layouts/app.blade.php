<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadSync')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f4f8;
        }

        /* NAVBAR */
        nav {
            background: #422c50;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        nav a:hover {
            background: rgba(255,255,255,0.15);
        }

        /* CONTAINER */
        .container {
            width: 100%;
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        /* BUTTON */
        .btn {
            background: #4427ae;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: auto;
        }

        .btn:hover {
            background: #219150;
        }

        /* ALERT */
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            background: #d4edda;
            color: #155724;
            border-radius: 5px;
        }

        /* RESPONSIVE RULES */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                flex-direction: column;
                gap: 8px;
                margin-top: 10px;
            }

            .container {
                margin: 15px;
                padding: 15px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<nav>
    <div style="color:white; font-weight:bold;">PadSync</div>

    <div class="nav-links">
        <a href="/">Home</a>
        <a href="/donate">Take Action</a>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

</body>
</html>