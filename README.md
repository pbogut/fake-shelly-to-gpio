# Fake Shelly To GPIO

It pretends to be Shelly switch and manipulates GPIO IN/OUT mode in the back.

My relay was designed for Arduino not Raspberry PI and is not working correctly
with the standard GPIO implementation. However switching GPIO mode from IN to OUT
is working reliably for switching relays on and off.

I use it as a Shelly switch in [moonraker](https://github.com/Arksine/moonraker).

## Installation 

```
cd /home/pi
git clone git@github.com:pbogut/fake-shelly-to-gpio
sudo ln -s /home/pi/fake-shelly-to-gpio/fake-shelly-to-gpio.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now fake-shelly-to-gpio.service
```

## Moonraker configuration

Add power option of `shelly` type. By default address should be `127.0.0.1:6622`
and output_id should match your GPIO pin.

```
[power my_printer]
type: shelly
address: 127.0.0.1:6622
output_id: 18
```
