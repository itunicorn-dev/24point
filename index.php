<?php
require_once 'Calculator.php';

$solutions = [];
$error = '';
$numbers = [];
$executionTime = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numbers = array_map(function($n) {
        return isset($_POST["number$n"]) ? (int)$_POST["number$n"] : null;
    }, [1, 2, 3, 4]);

    // 验证输入
    if (count(array_filter($numbers, function($n) { return $n >= 1 && $n <= 13; })) !== 4) {
        $error = '请输入4个1-13之间的整数！';
    } else {
        $calculator = new Calculator();
        $startTime = microtime(true);
        $solutions = $calculator->calculate($numbers);
        $executionTime = round((microtime(true) - $startTime) * 1000, 2); // 毫秒
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>24点计算器</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .number-input {
            width: 60px;
            height: 60px;
            font-size: 24px;
            text-align: center;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .number-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            outline: none;
        }
        .solution-card {
            background: white;
            border-radius: 12px;
            padding: 12px;
            margin: 8px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .solution-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="p-4">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 mt-10">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">24点计算器</h1>
        
        <form method="POST" class="space-y-6">
            <div class="flex flex-wrap justify-center gap-4">
                <?php for($i = 1; $i <= 4; $i++): ?>
                    <input type="number" 
                           name="number<?php echo $i; ?>" 
                           value="<?php echo isset($numbers[$i-1]) ? $numbers[$i-1] : ''; ?>"
                           min="1" 
                           max="13" 
                           required
                           class="number-input"
                           placeholder="<?php echo $i; ?>">
                <?php endfor; ?>
            </div>
            
            <div class="text-center">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold 
                               hover:bg-indigo-700 transition duration-200 ease-in-out
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    计算
                </button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="mt-6 p-4 bg-red-50 text-red-700 rounded-lg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($solutions)): ?>
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">计算结果</h2>
                    <span class="text-sm text-gray-500">用时：<?php echo $executionTime; ?>ms</span>
                </div>
                
                <?php if (empty($solutions)): ?>
                    <div class="solution-card bg-yellow-50 text-yellow-700">
                        没有找到解法！
                    </div>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($solutions as $solution): ?>
                            <div class="solution-card">
                                <?php echo htmlspecialchars($solution); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
