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
	    I can use a raspbery PI to create one LoRa gateway, to recive and parse the recived signals.
	    With LoRa I can also use this to send signals to the radio. 
	    I plan on making a big red cutdown button for `safty reasons'.
	    </li>
	    <li>
	    Cutdown meathods need investigating.
	    ODIN-I didn't have a cutdown, which could be helpful for preventing a sea-born exapition.
	    I have invested in some firework lighters, used to melt the nylon cord when a current is applied and upon provitional testing, this looks like a promising cutdown meathod.
	    </li>
	    <li>
	    Selection of a micro controller, or mabey a arm singal board computer.
	    I used the ATmega328p, which I could use again.
	    This time, however, I would wan't to do it without using the arduino bootloader, as this may save me some memmory.
	    I am also concidering going with the new rapberry pi zero, this is because it is, in theory, powerful enough to encode the output of a camara to SSDV and get liveish pictures back to earth.
	    </li>
	    <li>
	    Camaras are another aspect that I need to better implement.
	    ODIN-I was going to be using a cheep £20 go-pro clone.
	    This, although it worked, wasn't the best solution.
	    A better camara that is controlled by the flight computer is somthing I would hope to implement.
	    </li>
	    <lI>
	    Fabricating my own PCB is another thing I would like to do.
	    I've never had a PCB professionaly fabricated before, having only ever done it with a perminant marker and ferric chloride.
	    Makeing a tiny, multi-layer PCB would be fairly cool.
	    A 2 layer PCB can, if sourced correctly, cost near nothing (£1.62 each for 5!).
	    This would cost more (in terms of individual serface mount componants) than the prototype, but would be lighter and smaller.
	    </li>
	    <li>
	    Better payload design.
	    ODIN-I had a fairly poor payload design and materials.
	    Although it worked, it was inelegant and haphazard and as such I want to improve on the insulation board solution.
	    I would look into using `styrofoam' or small polystirine boxes.
	    </li>
	    <li>
	    An intresting meteorological study would be nice to find. 
	    I am looking into resarch that I could carry out in near space.
	    I can already mesure pressure, tempriture and humidity but a uneque experement would be nice to come up with.
	    </li>
	</ol>
    </p>
    <p>
    I am hopefull that ODIN-II payload will be an intresting project although it may take a while for me to compleat, as I am relitivly bussy at the moment.
    </p>
    <p><i>[More to be added and ammended]</i></p>
</div>

<?php echo_end(); ?>
