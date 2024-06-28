<?php 
// debug errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db.php'); 
include('add.php');
include('getList.php');
include('delete.php');
include('downloadCSV.php');

session_start(); // start (or resume) session to store toast message, errors, and previous input

// handle requests

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contact'])) {
    $response = addContact($conn);
    $_SESSION['toast'] = $response['message'];
    if($response['status'] !== 200) {
        $_SESSION['errors'] = $response['errors'];
        $_SESSION['previous'] = $response['previous'];
    }
    header("Location: index.php"); // prevents form resubmission when refreshing the page
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact'])) {
    $response = deleteContact($conn);
    $_SESSION['toast'] = $response['message'];
    header("Location: index.php"); // prevents form resubmission when refreshing the page
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download_csv'])) {
    downloadCSV($conn);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contacts App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
</head>
<body>
    <div id='toast' class="absolute bg-gray-200 rounded p-3 border-2 border-black bottom-5 transition-transform ease-out duration-300 -translate-x-full"></div> <!-- toast initially set to outside of the screen on the left -->
    
    <div class="p-5 bg-gray-100 h-screen flex flex-col">
        <h1 class="font-bold text-3xl mb-5">Contacts App</h1>
        <form action="index.php" method="post" class="items-start bg-white rounded p-5 shadow-md">
            <input type="hidden" name="add_contact" value="1">
            <h2 class="font-bold text-xl mb-3">Add New Contact</h2>
            <div class="flex flex-col">
                <label for="name" class="text-gray-700">Name</label>
                <input type="text" name="name" id="name" class="p-2 rounded-lg border border-gray-300 mt-1 shadow" 
                    value=  
                        <?php 
                            // check session for previous input
                            if(isset($_SESSION['previous']['name'])) {
                                echo $_SESSION['previous']['name'];
                                unset($_SESSION['previous']['name']); // clear session
                            }
                        ?>
                >
                <?php
                    // check session for errors
                    if(isset($_SESSION['errors']['name'])) {
                        echo "<p class='text-red-500 text-sm mt-1'>{$_SESSION['errors']['name']}</p>";
                        unset($_SESSION['errors']['name']); // clear session
                    }
                ?>
            </div>
            <div class="flex flex-col mt-2">
                <label for="email" class="text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="p-2 rounded-lg border border-gray-300 mt-1 shadow"
                    value=  
                        <?php 
                            // check session for previous input
                            if(isset($_SESSION['previous']['email'])) {
                                echo $_SESSION['previous']['email'];
                                unset($_SESSION['previous']['email']); // clear session
                            }
                        ?>
                >
                <?php
                    // check session for errors
                    if(isset($_SESSION['errors']['email'])) {
                        echo "<p class='text-red-500 text-sm mt-1'>{$_SESSION['errors']['email']}</p>";
                        unset($_SESSION['errors']['email']); // clear session
                    }
                ?>
            </div>
            <div class="flex flex-col mt-2">
                <label for="phone" class="text-gray-700">Phone</label>
                <input type="text" name="phone" id="phone" class="p-2 rounded-lg border border-gray-300 mt-1 shadow"
                    value=  
                        <?php 
                            // check session for previous input
                            if(isset($_SESSION['previous']['phone'])) {
                                echo $_SESSION['previous']['phone'];
                                unset($_SESSION['previous']['phone']); // clear session
                            }
                        ?>
                >
                <?php
                    // check session for errors
                    if(isset($_SESSION['errors']['phone'])) {
                        echo "<p class='text-red-500 text-sm mt-1'>{$_SESSION['errors']['phone']}</p>";
                        unset($_SESSION['errors']['phone']); // clear session
                    }
                ?>
            </div>
            <button type="submit" class="bg-slate-900 text-white py-2 px-5 rounded mt-5 shadow text-sm font-bold text-gray-50 hover:brightness-75">Add Contact</button>
        </form>

        <div class="bg-white shadow-md mt-3 flex-1 rounded p-7 overflow-auto">
            <div class="flex gap-3 items-center mb-3">
                <h1 class="font-bold text-xl">Contact List</h1>
                <?php
                    // check if there are contacts to download
                    $list = getlist($conn);
                    if ($list->num_rows > 0) {
                        echo '
                            <form class="size-7 hover:brightness-75" method="GET">
                                <input type="hidden" name="download_csv" value="1">
                                <button type="submit">
                                    <img src="./images/exportcsv.png" alt="download_csv">
                                </button>
                            </form>
                        ';
                    }
                ?>
            </div>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="border-b-2 border-gray-300 p-2 text-left">Name</th>
                        <th class="border-b-2 border-gray-300 p-2 text-left">Email</th>
                        <th class="border-b-2 border-gray-300 p-2 text-left">Phone</th>
                        <th class="border-b-2 border-gray-300 p-2 text-left">Added At</th>
                        <th class="border-b-2 border-gray-300 p-2 text-left"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // print out the list of contacts
                        $list = getlist($conn);
                        if ($list->num_rows > 0) {
                            while($row = $list->fetch_assoc()) { // for each row in the list
                                $timestamp = strtotime($row["created_at"]);
                                echo "<tr>";
                                echo "<td class='border-b border-gray-300 p-2'>" . $row["name"]. "</td>";
                                echo "<td class='border-b border-gray-300 p-2'>" . $row["email"]. "</td>";
                                echo "<td class='border-b border-gray-300 p-2'>" . $row["phone"]. "</td>";
                                echo "<td class='border-b border-gray-300 p-2'>" . date('d M Y', $timestamp). "</td>";
                                echo "<td class='border-b border-gray-300 p-2 flex justify-center'>
                                        <form action='index.php' method='POST' class='mt-auto mb-auto'>
                                            <input type='hidden' name='delete_contact' value='1'>
                                            <input type='hidden' name='id' value='" . $row["id"]. "'>
                                            <button type='submit' class='bg-red-500 text-white py-1 px-3 rounded shadow text-sm font-bold text-gray-50 hover:brightness-75'>Delete</button>
                                        </form>
                                      </td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'><p class='mt-5'>No contacts found</p></td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // function definition to show toast
        function showToast(message) {
            let toast = document.getElementById('toast');
            toast.innerHTML = message;
            // animate toast
            void toast.offsetWidth; // force reflow
            toast.classList.remove('-translate-x-full');
            toast.classList.add('translate-x-5');
            setTimeout(() => {
                toast.classList.remove('translate-x-5');
                toast.classList.add('-translate-x-full');
            }, 2500);
        }

        <?php
            // check session for toast message
            if(isset($_SESSION['toast'])) {
                echo "showToast('{$_SESSION['toast']}');"; // call function to show toast
                unset($_SESSION['toast']); // clear session
            }
        ?>
    </script>
</body>
</html>
