    <!DOCTYPE html>
<html>
<head>
    <title>Calculadora PHP</title>
</head>
<body>
    <h2>Calculadora de Dois Números</h2>
    <form method="post">
        Número 1: <input type="number" name="num1" step="any" required><br>
        Número 2: <input type="number" name="num2" step="any" required><br>
        Operação:
        <select name="operacao">
            <option value="soma">+</option>
            <option value="subtracao">-</option>
            <option value="multiplicacao">*</option>
            <option value="divisao">/</option>
        </select><br>
        <input type="submit" value="Calcular">
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $num1 = $_POST["num1"];
        $num2 = $_POST["num2"];
        $operacao = $_POST["operacao"];
        $resultado = "";
        switch ($operacao) {
            case "soma":
                $resultado = $num1 + $num2;
                break;
            case "subtracao":
                $resultado = $num1 - $num2;
                break;
            case "multiplicacao":
                $resultado = $num1 * $num2;
                break;
            case "divisao":
                if ($num2 != 0) {
                    $resultado = $num1 / $num2;
                } else {
                    $resultado = "Erro: divisão por zero!";
                }
                break;
        }
        echo "<h3>Resultado: $resultado</h3>";
    }
    ?>
</body>
</html>
    # Code Citations

## License: unknown

https://github.
com/maxleesilva/calculadora-php/tree/99ee9af3c630f00f64d640c73e348cb0f6bae92b/calculo.php

```
Operação:
        <select name="operacao">
            <option value="soma">+</option>
            <option value="subtracao">-</option>
            <option value="multiplicacao">*</option>
            <option value="divisao
```


## License: unknown
https://github.com/Rafaelsp99/PHP-CALCULADORA/tree/f5bd7ed49df6ce6f66bf877f88137ad5a971651d/index.php

```
:
        <select name="operacao">
            <option value="soma">+</option>
            <option value="subtracao">-</option>
            <option value="multiplicacao">*</option>
            <option value="divisao"
```


## License: unknown
https://github.com/drdpedroso/dw2-course/tree/a51bcb14738cfff6a4bb773f15e5bdf253742cca/exercicio4/index.php

```
option value="soma">+</option>
            <option value="subtracao">-</option>
            <option value="multiplicacao">*</option>
            <option value="divisao">/</option>
        </select>
```

<!DOCTYPE html>
</body>
</html>

