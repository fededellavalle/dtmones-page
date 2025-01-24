<?php
session_start();

$host = "localhost:3307";
$user = "root";
$password = "";
$dbname = "clientesdb";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$error = "";

// Procesar formulario de login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header("Location: /pages/login/dashboard.php"); // Redirige a otra página tras el login
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DTMones</title>
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/assets/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style-login.css">
</head>

<body>

    <header>
        <a href="/index.html">
            <img src="/assets/images/estrellaDTM.png" alt="Logo">
        </a>

        <div class="menu-icon">
            <i class="bi bi-list"></i>
        </div>
    </header>

    <!-- Overlay oscuro -->
    <div class="overlay"></div>

    <!-- Menú lateral -->
    <div class="side-menu">
        <button class="close-menu">&times;</button>
        <nav>
            <a href="/index.html">Home</a>
            <a href="/pages/who-we-are.html">Who we are</a>
            <a href="/pages/clients.html">Clients</a>
            <a href="/pages/legends.html">Legends</a>
            <a href="/pages/media.html">Media</a>
            <a href="/pages/contact-us.html">Contact us</a>
        </nav>
    </div>
    <div class="login-container">
        <div class="login-box">
            <h2>Iniciar Sesión</h2>
            <?php if (!empty($error)): ?>
                <p class="error-message">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
        </div>
    </div>

    <footer class="footer text-white">
        <div class="container d-flex justify-content-between align-items-center flex-column flex-md-row">
            <div class="footer-logo text-center mb-4 mb-md-0">
                <img src="/assets/images/logo3.png" alt="DTMones Basketball Agency" class="img-fluid mb-2"
                    style="max-width: 150px;" />
                <p class="mb-0">DTMones Basketball Agency</p>
            </div>
            <div class="footer-contact text-center text-md-end">
                <p class="mb-0">Tel: 35534343</p>
                <p class="mb-0">dtmones@gmail.com.ar</p>
                <p class="mb-0">www.dtmones.com.ar</p>
                <hr class="my-2 mx-auto mx-md-0" style="border-top: 1px solid #ffffff; width: 80%;" />
                <div class="social-icons d-flex justify-content-center justify-content-md-end">
                    <a href="https://www.facebook.com/p/DTMones-Basketball-Agency-100066988350727/"
                        class="text-white me-3"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/dtm.ones/" class="text-white me-3"><i
                            class="bi bi-instagram"></i></a>
                    <a href="https://twitter.com/dtm_ones" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                    <a href="https://www.youtube.com/channel/UCSvEghAAbhjs1HpLl68FIvw" class="text-white me-3"><i
                            class="bi bi-youtube"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 div-copy">
            <p class="mb-0">&copy; 2025 DTMones Basketball Agency. Todos los derechos
                reservados.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="/js/header.js"></script>
</body>

</html>