<?php
declare(strict_types=1);

require_once __DIR__ . '/config/status_codes.php';

function esc(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// POSTを安全に取得（未指定はリダイレクト）
$answer_code = filter_input(INPUT_POST, 'answer_code', FILTER_UNSAFE_RAW);
$option      = filter_input(INPUT_POST, 'option',       FILTER_UNSAFE_RAW);

if (!is_string($answer_code) || !is_string($option) || $answer_code === '' || $option === '') {
    header('Location: index.php?error=missing', true, 302);
    exit;
}

$answer_code = trim($answer_code);
$option      = trim($option);

// 正解（説明）を status_codes から検索
$code = null;
$description = null;

if (isset($status_codes) && is_array($status_codes)) {
    foreach ($status_codes as $status_code) {
        if (!isset($status_code['code'])) continue;
        if ((string)$status_code['code'] === $answer_code) {
            $code = (string)$status_code['code'];
            $description = (string)($status_code['description'] ?? '');
            break;
        }
    }
}

// 存在しないコードが来た場合は戻す
if ($code === null) {
    header('Location: index.php?error=invalid_code', true, 302);
    exit;
}

// 判定（ユーザーの回答 vs 正解コード）
$result = ($option === $code);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Code Quiz</title>
    <link rel="stylesheet" href="css/sanitize.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/result.css">
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <!-- php03 のトップに戻したいなら href="/php03/" などに変更 -->
            <a class="header__logo" href="/">Status Code Quiz</a>
        </div>
    </header>

    <main>
        <div class="result__content">
            <div class="result">
                <?php if ($result): ?>
                    <h2 class="result__text--correct">正解</h2>
                <?php else: ?>
                    <h2 class="result__text--incorrect">不正解</h2>
                <?php endif; ?>
            </div>

            <div class="answer-table">
                <table class="answer-table__inner">
                    <tr class="answer-table__row">
                        <th class="answer-table__header">ステータスコード</th>
                        <td class="answer-table__text"><?= esc($code) ?></td>
                    </tr>
                    <tr class="answer-table__row">
                        <th class="answer-table__header">説明</th>
                        <td class="answer-table__text"><?= esc($description) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
