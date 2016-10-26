<?php require('php/include.php');
echo_start(4, "ODIN-II Plan");
?>

<div class="container">
    <div class="page-header">
        <h1>Plan</h1>
    </div>
    <p>
        <ol>
	    <li>
	    I need to investigate and test LoRa radio modules.
	    I can use a raspberry PI to create one LoRa gateway, to receive and parse the received signals. 
	    With LoRa I can also use this to send signals to the radio. 
	    I plan on making a big red cutdown button for `safety reasons'.
	    </li>
	    <li>
	    Cutdown methods need investigating. 
	    ODIN-I didn't have a cutdown, which could be helpful for preventing a sea-born exception. 
	    I have invested in some firework lighters, used to melt the nylon cord when a current is applied and upon provisional testing, this looks like a promising cutdown method.
	    </li>
	    <li>
	    Selection of a microcontroller or maybe an arm single board computer. 
	    I used the ATmega328p, which I could use again. 
	    This time, however, I would want to do it without using the Arduino bootloader, as this may save me some memory, and be more interesting. 
	    I am also considering going with the new raspberry pi zero, this is because it is, in theory, powerful enough to encode the output of a camera to SSDV and get liveish pictures back to earth.
	    </li>
	    <li>
	    Cameras are another aspect that I need to better implement. 
	    ODIN-I was going to be using a cheap £20 go-pro clone. 
	    This, although it worked, wasn't the best solution. 
	    A better camera that is controlled by the flight computer is something I would hope to implement.
	    </li>
	    <lI>
	   
	    Fabricating my own PCB is another thing I would like to do. 
	    I've never had a PCB professionally fabricated before, having only ever done it with a permanent marker and ferric chloride. 
	    Making a tiny, multi-layer PCB would be fairly cool. 
	    A 2 layer PCB can if sourced correctly, cost near nothing (£1.62 each for 5!). 
	    This would cost more (in terms of individual surface mount components) than the prototype but would be lighter and smaller.

	    </li>
	    <li>
	    Better payload design. 
	    ODIN-I had a fairly poor payload design and materials. 
	    Although it worked, it was inelegant and haphazard and as such, I want to improve on the insulation board solution. 
	    I would look into using `styrofoam' or small polystyrene boxes.
	    </li>
	    <li>
	    An interesting meteorological study would be nice to find. 
	    I am looking into research that I could carry out in near space. 
	    I can already measure pressure, temperature and humidity but a unique experiment would be nice to come up with.
	    </li>
	</ol>
    </p>
    <p>
    An interesting meteorological study would be nice to find. 
    I am looking into research that I could carry out in near space. 
    I can already measure pressure, temperature and humidity but a unique experiment would be nice to come up with.
    </p>
    <p><i>[More to be added and ammended]</i></p>
</div>

<?php echo_end(); ?>
