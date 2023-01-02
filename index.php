<?php

class GPIO
{
    public const IN = 'IN';
    public const OUT = 'OUT';

    public function readAll()
    {
        $gpioInfo = shell_exec('gpio allreadall');
        $result = [];
        $lines = explode("\n", $gpioInfo);
        foreach ($lines as $line) {
            $hasMatch = preg_match_all('/\|(.*?)\|(.*?)\|(.*?)\|/', $line, $m);
            if (!$hasMatch) {
                continue;
            }
            for ($i = 0; $i < $hasMatch; $i++) {
                $pin = trim($m[1][$i]);
                $mode = trim($m[2][$i]);
                $value = trim($m[3][$i]);
                if ($pin && $pin != 'Pin') {
                    $result[(int) $pin] = [
                        'pin' => $pin,
                        'mode' => $mode,
                        'value' => $value,
                    ];
                }
            }
        }
        return $result;
    }

    public function getPinMode($pin)
    {
        $info = $this->readAll();
        return $info[$pin]['mode'] ?? null;
    }

    public function setPinMode($pin, $mode)
    {
        shell_exec("gpio -g mode $pin $mode");
        shell_exec("gpio -g write $pin 1");
    }
}

class Shelly
{

    const ON = 'on';
    const OFF = 'off';

    /** @var GPIO $gpio */
    protected $gpio = null;

    public function __construct($gpio)
    {
        $this->gpio = $gpio;
    }

    public function handle($uri)
    {
        $id = preg_replace('/.*?relay\/(\d+).*/', '\1', $uri);
        $action = $_GET['turn'] ?? null;
        if ($action == self::ON) {
            $isOn = 1;
            $this->turnOn($id);
        } elseif ($action == self::OFF) {
            $isOn = 0;
            $this->turnOff($id);
        } else {
            $isOn = $this->isOn($id);
        }

        return ['ison' => (int) $isOn];
    }

    public function turnOn($id)
    {
        $this->gpio->setPinMode($id, GPIO::OUT);
    }

    public function turnOff($id)
    {
        $this->gpio->setPinMode($id, GPIO::IN);
    }

    public function isOn($id)
    {
        return $this->gpio->getPinMode($id) == GPIO::OUT;
    }
}

$gpio = new GPIO;
$shelly = new Shelly($gpio);

$path = $_SERVER['REQUEST_URI'] ?? "";
$result = $shelly->handle($path);
echo json_encode($result);
