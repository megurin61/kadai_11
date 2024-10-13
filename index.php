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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// レシピをすべて取得
$sql = "SELECT * FROM recipes";
$result = $conn->query($sql);

// SQLの実行エラーチェック
if (!$result) {
    die("クエリエラー: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レシピ一覧</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>レシピ一覧</h1>
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>レシピ名</th>
                <th>材料</th>
                <th>作り方</th>
                <th>画像</th>
                <th>登録日時</th>
            </tr>

            <?php
            if ($result->num_rows > 0) {
                // レコードを1つずつフェッチして表示
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ingredients']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['instructions']) . "</td>";

                    // 画像の表示処理
                    if (!empty($row['image'])) {
                        echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='レシピ画像' width='100'></td>";
                    } else {
                        echo "<td>画像なし</td>";
                    }

                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "</tr>";
                }
            } else {
                // レシピがない場合のメッセージ
                echo "<tr><td colspan='6'>レシピがありません。</td></tr>";
            }
            ?>
        </table>

        <p><a href="add_recipe.php">レシピを追加する</a></p>
    </div>
</body>
</html>

<?php
// データベース接続を閉じる
$conn->close();
?>
