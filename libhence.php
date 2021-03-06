<?php
define('HENCE_FALSE', '0');
define('HENCE_TRUE',  '1');

$Stack = array();

function __lcall__()
{
    $name = 'hence_' . __pop__();
    $name();
}

function __depth__()
{
    global $Stack;

    __push__((string) count($Stack));
}

function __pop__()
{
    global $Stack;

    return array_pop($Stack);
}

function __push__($s)
{
    global $Stack;

    $Stack[] = $s;
}

function hence_and()
{
    $y = __pop__();
    $x = __pop__();
    __push__(($x != HENCE_FALSE && $y != HENCE_FALSE) ? (HENCE_TRUE) :
        (HENCE_FALSE));
}

function hence_bitwise_and()
{
    __push__((string) (__pop__() & __pop__()));
}

function hence_bitwise_not()
{
    __push__((string) ~((int) __pop__()));
}

function hence_bitwise_or()
{
    __push__((string) (__pop__() | __pop__()));
}

function hence_bitwise_shift_left()
{
    $y = (int) __pop__();
    $x = (int) __pop__();
    __push__((string) ($y << $x));
}

function hence_bitwise_shift_right()
{
    $y = (int) __pop__();
    $x = (int) __pop__();
    __push__((string) ($y >> $x));
}

function hence_bitwise_xor()
{
    __push__((string) ((int) __pop__() ^ (int) __pop__()));
}

function hence_call()
{
    global $Functions;

    $x = __pop__();
    if (isset($Functions[$x])) {
        $Functions[$x]();
    } else {
        file_put_contents('php://stderr', 'Runtime error' . PHP_EOL);
        exit(1);
    }
}

function hence_concatenate()
{
    $y = __pop__();
    $x = __pop__();
    __push__($y . $x);
}

function hence_debug()
{
    __depth__();
    $depth = __pop__();

    $v = array();
    for ($i = 0; $i < $depth; ++$i) {
        $v[] = __pop__();
    }
    echo '[ ';
    for ($i = count($v) - 1; $i > 0; --$i) {
        echo '"' . $v[$i] . '", ';
    }
    echo '"' . $v[0] . '"';
    echo ' ]', PHP_EOL;
    while (count($v) > 0) {
        __push__(array_pop($v));
    }
}

function hence_depth()
{
    __depth__();
}

function hence_divide()
{
    $y = (int) __pop__();
    $x = (int) __pop__();
    __push__((string) ((int) ($y / $x)));
}

function hence_drop()
{
    __pop__();
}

function hence_equal()
{
    __push__((__pop__() == __pop__()) ? (HENCE_TRUE) : (HENCE_FALSE));
}

function hence_if()
{
    global $Functions;

    $cond_func  = __pop__();
    $true_func  = __pop__();
    $false_func = __pop__();

    if (isset($Functions[$cond_func])) {
        $Functions[$cond_func]();
    } else {
        file_put_contents('php://stderr', 'Runtime error' . PHP_EOL);
        exit(1);
    }
    $result = __pop__();
    if ($result != HENCE_FALSE) {
        if (isset($Functions[$true_func])) {
            $Functions[$true_func]();
        } else {
            file_put_contents('php://stderr', 'Runtime error' . PHP_EOL);
            exit(1);
        }
    } else {
        if (isset($Functions[$false_func])) {
            $Functions[$false_func]();
        } else {
            file_put_contents('php://stderr', 'Runtime error' . PHP_EOL);
            exit(1);
        }
    }
}

function hence_json_rpc()
{
    $method = __pop__();
    $params = __pop__();
    echo '{"method":"' . $method . '","params":' . $params . ',"id":1}',
        PHP_EOL;
}

function hence_length()
{
    __push__((string) strlen(__pop__()));
}

function hence_less_than()
{
    $y = (int) __pop__();
    $x = (int) __pop__();
    __push__(($y < $x) ? (HENCE_TRUE) : (HENCE_FALSE));
}

function hence_modulo()
{
    $y = (int) __pop__();
    $x = (int) __pop__();
    __push__((string) ($y % $x));
}

function hence_multiply()
{
    __push__((string) ((int) __pop__() * (int) __pop__()));
}

function hence_not()
{
    __push__((__pop__() != HENCE_FALSE) ? (HENCE_FALSE) : (HENCE_TRUE));
}

function hence_or()
{
    $y = __pop__();
    $x = __pop__();
    __push__(($x != HENCE_FALSE || $y != HENCE_FALSE) ? (HENCE_TRUE) :
        (HENCE_FALSE));
}

function hence_pick()
{
    $n = (int) __pop__() + 1;
    $v = array();
    for ($i = 0; $i < $n; ++$i) {
        $v[] = __pop__();
    }
    for ($i = count($v) - 1; $i >= 0; --$i) {
        __push__($v[$i]);
    }
    __push__($v[count($v) - 1]);
}

function hence_print()
{
    echo __pop__();
}

function hence_roll()
{
    $n = (int) __pop__();

    $v = array();
    for ($i = 0; $i <= $n; ++$i) {
        $v[] = __pop__();
    }
    $x = array_pop($v);
    while (count($v) > 0) {
        __push__(array_pop($v));
    }
    __push__($x);
}

function hence_substring()
{
    $string = __pop__();
    $start = (int) __pop__();
    $length = (int) __pop__();
    __push__(substr($string, $start, $length));
}

function hence_subtract()
{
    $y = (int) __pop__();
    $x = (int) __pop__();
    __push__((string) ($x - $y));
}

function hence_target()
{
    __push__('php');
}

function hence_while()
{
    global $Functions;

    $cond_func = __pop__();
    $loop_func = __pop__();

    if ( !isset($Functions[$cond_func])) {
        file_put_contents('php://stderr', 'Runtime error' . PHP_EOL);
        exit(1);
    }
    if ( !isset($Functions[$loop_func])) {
        file_put_contents('php://stderr', 'Runtime error' . PHP_EOL);
        exit(1);
    }
    $Functions[$cond_func]();
    $result = __pop__();
    while ($result != HENCE_FALSE) {
        $Functions[$loop_func]();

        $Functions[$cond_func]();
        $result = __pop__();
    }
}
