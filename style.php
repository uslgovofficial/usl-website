<?php
// Export CSS
header("Content-type: text/css");
?>

<?php
echo "
body {
    background-color: #333;
    color: #fff;
    font-size: 16px;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

table {
    background-color: #444;
    border-collapse: collapse;
    width: 100%;
}

.centertable {
    background-color: #333;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

td {
    background-color: #444;
    padding: 10px;
    border: 1px solid #555;
}

i {
    font-size: 18px;
    font-style: italic;
}

a {
    font-weight: bold;
    text-decoration: none;
    color: #66d9ef;
    transition: color 0.2s ease;
}

a:hover {
    color: #fff;
}

h1 {
    color: #66d9ef;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
}

h2 {
    color: #fff;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
}

h3 {
    color: #fff;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

/* Responsive design */
@media only screen and (max-width: 768px) {
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td {
        width: 100%;
        padding: 10px;
        border: 1px solid #555;
    }
    .centertable {
        padding: 10px;
    }
}

@media only screen and (max-width: 480px) {
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td {
        width: 100%;
        padding: 5px;
        border: 1px solid #555;
    }
    .centertable {
        padding: 5px;
    }
}
";
?>