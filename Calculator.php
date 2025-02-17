<?php

class Calculator {
    private array $operators = ['+', '-', '*', '/'];
    private array $solutions = [];
    private array $normalizedExpressions = [];

    public function calculate(array $numbers): array {
        $this->solutions = [];
        $this->normalizedExpressions = [];
        // 获取所有可能的数字排列
        $permutations = $this->getPermutations($numbers);
        
        // 对每个排列尝试所有可能的运算符组合
        foreach ($permutations as $nums) {
            foreach ($this->operators as $op1) {
                foreach ($this->operators as $op2) {
                    foreach ($this->operators as $op3) {
                        // 尝试不同的括号组合
                        // ((a op1 b) op2 c) op3 d
                        $this->tryExpression($nums[0], $nums[1], $nums[2], $nums[3], $op1, $op2, $op3, 1);
                        // (a op1 b) op2 (c op3 d)
                        $this->tryExpression($nums[0], $nums[1], $nums[2], $nums[3], $op1, $op2, $op3, 2);
                        // a op1 (b op2 (c op3 d))
                        $this->tryExpression($nums[0], $nums[1], $nums[2], $nums[3], $op1, $op2, $op3, 3);
                        // a op1 ((b op2 c) op3 d)
                        $this->tryExpression($nums[0], $nums[1], $nums[2], $nums[3], $op1, $op2, $op3, 4);
                        // (a op1 (b op2 c)) op3 d
                        $this->tryExpression($nums[0], $nums[1], $nums[2], $nums[3], $op1, $op2, $op3, 5);
                    }
                }
            }
        }
        
        return $this->solutions;
    }

    private function tryExpression(int $a, int $b, int $c, int $d, string $op1, string $op2, string $op3, int $pattern): void {
        $result = 0;
        $expr = '';
        $normalizedExpr = '';
        
        switch ($pattern) {
            case 1: // ((a op1 b) op2 c) op3 d
                $temp1 = $this->compute($a, $b, $op1);
                if ($temp1 === false) return;
                $temp2 = $this->compute($temp1, $c, $op2);
                if ($temp2 === false) return;
                $result = $this->compute($temp2, $d, $op3);
                if ($result === false) return;
                $expr = "(($a $op1 $b) $op2 $c) $op3 $d";
                $normalizedExpr = $this->normalizeExpression([$a, $b, $c, $d], [$op1, $op2, $op3], $result);
                break;
                
            case 2: // (a op1 b) op2 (c op3 d)
                $temp1 = $this->compute($a, $b, $op1);
                if ($temp1 === false) return;
                $temp2 = $this->compute($c, $d, $op3);
                if ($temp2 === false) return;
                $result = $this->compute($temp1, $temp2, $op2);
                if ($result === false) return;
                $expr = "($a $op1 $b) $op2 ($c $op3 $d)";
                $normalizedExpr = $this->normalizeExpression([$a, $b, $c, $d], [$op1, $op2, $op3], $result);
                break;
                
            case 3: // a op1 (b op2 (c op3 d))
                $temp1 = $this->compute($c, $d, $op3);
                if ($temp1 === false) return;
                $temp2 = $this->compute($b, $temp1, $op2);
                if ($temp2 === false) return;
                $result = $this->compute($a, $temp2, $op1);
                if ($result === false) return;
                $expr = "$a $op1 ($b $op2 ($c $op3 $d))";
                $normalizedExpr = $this->normalizeExpression([$a, $b, $c, $d], [$op1, $op2, $op3], $result);
                break;
                
            case 4: // a op1 ((b op2 c) op3 d)
                $temp1 = $this->compute($b, $c, $op2);
                if ($temp1 === false) return;
                $temp2 = $this->compute($temp1, $d, $op3);
                if ($temp2 === false) return;
                $result = $this->compute($a, $temp2, $op1);
                if ($result === false) return;
                $expr = "$a $op1 (($b $op2 $c) $op3 $d)";
                $normalizedExpr = $this->normalizeExpression([$a, $b, $c, $d], [$op1, $op2, $op3], $result);
                break;
                
            case 5: // (a op1 (b op2 c)) op3 d
                $temp1 = $this->compute($b, $c, $op2);
                if ($temp1 === false) return;
                $temp2 = $this->compute($a, $temp1, $op1);
                if ($temp2 === false) return;
                $result = $this->compute($temp2, $d, $op3);
                if ($result === false) return;
                $expr = "($a $op1 ($b $op2 $c)) $op3 $d";
                $normalizedExpr = $this->normalizeExpression([$a, $b, $c, $d], [$op1, $op2, $op3], $result);
                break;
        }
        
        if ($result === 24 && !in_array($normalizedExpr, $this->normalizedExpressions)) {
            $this->solutions[] = $expr;
            $this->normalizedExpressions[] = $normalizedExpr;
        }
    }

    private function normalizeExpression(array $numbers, array $operators, float $result): string {
        // 创建表达式的数学特征
        $terms = [];
        
        // 处理乘法和除法链
        $mulDivChain = [];
        $currentProduct = 1;
        
        foreach ($numbers as $i => $num) {
            if ($i > 0) {
                $op = $operators[$i - 1];
                if ($op === '*' || $op === '/') {
                    if ($op === '*') {
                        $currentProduct *= $num;
                    } else {
                        $currentProduct /= $num;
                    }
                    continue;
                }
            }
            if ($currentProduct !== 1) {
                $mulDivChain[] = $currentProduct;
                $currentProduct = 1;
            }
            $mulDivChain[] = $num;
        }
        
        if ($currentProduct !== 1) {
            $mulDivChain[] = $currentProduct;
        }
        
        // 对乘除链进行排序
        sort($mulDivChain);
        
        // 处理加法和减法
        $addSubTerms = [];
        foreach ($operators as $op) {
            if ($op === '+' || $op === '-') {
                $addSubTerms[] = $op;
            }
        }
        sort($addSubTerms);
        
        // 创建规范化字符串
        return implode('_', $mulDivChain) . '|' . implode('', $addSubTerms) . '=' . $result;
    }

    private function compute(int|float $a, int|float $b, string $op): int|float|false {
        switch ($op) {
            case '+': return $a + $b;
            case '-': return $a - $b;  // 允许负数，因为中间结果可以为负
            case '*': return $a * $b;
            case '/':
                // 除数不能为0，且结果必须是有限数
                return ($b !== 0 && is_finite($a / $b)) ? $a / $b : false;
            default:
                return false;
        }
    }

    private function getPermutations(array $numbers): array {
        if (count($numbers) <= 1) {
            return [$numbers];
        }

        $result = [];
        $used = [];
        foreach ($numbers as $i => $num) {
            // 跳过重复的数字
            if (isset($used[$num])) continue;
            $used[$num] = true;
            
            $remaining = array_merge(
                array_slice($numbers, 0, $i),
                array_slice($numbers, $i + 1)
            );
            foreach ($this->getPermutations($remaining) as $permutation) {
                $result[] = array_merge([$num], $permutation);
            }
        }
        return $result;
    }
}
