<?php
// エラーメッセージを表示する設定（デバッグ用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// データベース接続情報
$servername = "";
$username = "";
$password = "";
$dbname = "";

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続確認
if ($conn->connect_error) {
    die("データベース接続失敗: " . $conn->connect_error);
}

// フォームの送信を確認
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // フォームデータを取得
    $title = isset($_POST['title']) ? $conn->real_escape_string($_POST['title']) : '';
    $ingredients = isset($_POST['ingredients']) ? $conn->real_escape_string($_POST['ingredients']) : '';
    $instructions = isset($_POST['instructions']) ? $conn->real_escape_string($_POST['instructions']) : '';

    // すべての項目が入力されているか確認
    if (empty($title) || empty($ingredients) || empty($instructions)) {
        echo "すべての項目を入力してください。";
        exit;
    }

    // 画像アップロード処理
    $image = NULL; // 初期値はNULL
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        // uploads ディレクトリが存在しない場合は作成
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // 画像ファイル形式の確認
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $valid_extensions)) {
            echo "許可されている画像形式は JPG, JPEG, PNG, GIF のみです。";
            exit;
        }

        // 画像のアップロードを試行
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            echo "画像のアップロードに失敗しました。";
            exit;
        }
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        echo "画像のアップロード中にエラーが発生しました。";
        exit;
    }

    // レシピをデータベースに挿入
    $stmt = $conn->prepare("INSERT INTO recipes (title, ingredients, instructions, image) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("プリペアステートメントの作成に失敗しました: " . $conn->error);
    }

    $stmt->bind_param("ssss", $title, $ingredients, $instructions, $image);

    if ($stmt->execute()) {
        echo "レシピが正常に登録されました！";
    } else {
        echo "エラー: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>レシピ登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>レシピを登録</h1>
        <form action="add_recipe.php" method="post" enctype="multipart/form-data">
            <label for="title">レシピ名:</label>
            <input type="text" name="title" id="title" required><br><br>

            <label for="ingredients">材料:</label>
            <textarea name="ingredients" id="ingredients" required></textarea><br><br>

            <label for="instructions">作り方:</label>
            <textarea name="instructions" id="instructions" required></textarea><br><br>

            <label for="image">画像をアップロード:</label>
            <input type="file" name="image" id="image"><br><br>

            <input type="submit" value="レシピを登録">
        </form>
    </div>
</body>
</html>

