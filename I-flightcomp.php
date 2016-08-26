<?php require('php/include.php');
echo_start(3, "ODIN-I Computor");
?>

<div class="container">
    <div class="page-header">
        <h1>The flight computor</h1>
    </div>
    <h3>The hardware</h3>
    <p>
        Finaly all of the bits and peices arrived (postage from china is annoyingly slow).
        We can now construct a robust and reliable tracking beacon flight computer.
	The primary function of this device will be to broadcast its location to earth so that I can find the payload when it lands.
	This is achived through the use of a uBlox GPS module and a 434MHz ISM transmitter [link].
	The microcontroller I am going to be useing is the ATmega 328P.
	The low specs of this chip required some neat tricks to achive all I wanted it to do.
	2K of ram is tiny in todays world and only having one hardware UART is very annoying.
	The reason to use this is that the power usage is tiny.
	This is critical as it will allow me to reduce weight when it comes to the battaries (I can get ~20 hours use out of 2 AAs*).
	The SPI interface on the ATmega is used to allow for writing to an SD card.
	This allows for the creation of a detailed flight log if I can recover the payload.
    </p>
    <p>
        The reasons behind my specific hardware choices came down mosly to price.
	Initialy I was going to do by own circut for the ATmega 328P but I descoverd that I could get a tiny breakout board for less than the cost of the chip plus the componants required (~£2).
	The GPS module was the cheepest uBlox serial breakout board was ~£8.
	This was again cheeper than buying the uBlox chip on its own and even came with a ceramic antenna.
	The SD card breakout cost ~£3.
	The most expencive part was the radio at a massive £21.
	I perchaced 2 DHT22 moduled for around £3 each but I broke one so I can now only messure the external tempriture.
	So for less than £40 I built the tracking system.
    </p>
    <p>
        [INSERT CIRCUT DIAGRAMME HERE]
    </p>
    <h3>The code</h3>
    <p>
        Coding this was a challange.
	This was mostly down to the amount of RAM avalable on the ATmega 328P.
	2048 bytes of RAM quickly vanishes when dealing with what I want to do.
	I needed to be very prudent about allocating memory to anything.
	There are workarounds (you can write data to an EPROM and get an extra 1k for instance) but I mangaged without them.
    </p>
    <p>
        The following is the final peice of code that will be used in the tracking becon.
        I commented it for you to read through.
    </p>
    [INSERT CODE HERE]
</div>
<?php echo_end(); ?>
