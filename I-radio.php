<?php require('php/include.php');
echo_start(3, "ODIN-I Communications");
?>
    <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
        <h1>Radio Communications for ODIN-I</h1>
      </div>
        <p>In planning our high altitude balloons we found that we needed a method to send information back down to earth. 
        This is necessary for recovering the payload and forgetting, in real time, information such as pictures, humidity, and altitude.
        To achieve this we explored several methods of sending data.</p>
        <h3>GSM</h3>
        <p>The most obvious first solution was to use the GSM network.
        Send up the balloon with a mobile phone to 'text' us back pictures and information.
        This, however, on further investigation becomes a terrible way to get live information from a payload 30,000m in the sky.
        This is because the range of gsm low and we cannot guarantee that the areas the balloon floats into will be covered.
        This alongside it being a very boring way to do it caused us to explore other methods.</p>
        <h3>APRS</h3>
        <p>We investigated amateur radio as a method of sending information back to earth.
        This is possible from a technical standpoint because of a technology called APRS.
        Automatic Packet Reporting Systems provide the user with a way to transmit information (such as location and any scientific data we might want to send) out into the world.
        APRS is a widely adopted protocol and so there are many listening stations around the country who report any APRS packets they receive to the internet.
        This would mean that we have the potential to find our balloon even if we couldn't pick up the transmissions directly.
        This is, unfortunately, illegal to do as the amateur radio license in the UK (reasons to move to America) explicitly forbids the operating of a radio above ground level.</p>
        <h3>ISM Bands</h3>
        <p>Now, having lost hope, we ventured onto the internet.
        We found a group called the UK high altitude society.
        They recommended the use of the 70cm ISM band.
        This is an unlicensed band used for 'industrial, scientific and medical' applications.
        Not only is this method legal but it is also far less expensive than an APRS beacon.
        We decided to use the <a href="http://www.radiometrix.com/content/ntx2b">Radiometrix NTX2B</a> as our transmitter which, at the time of writing, cost us in the region of £21.</p>

        <p>The NTX2B needed to be controlled by a microprocessor and at this point, we had a choice. The Arduino vs The Raspberry Pi.</p>
        <h3>Micro-processors</h3>
        <p>The Arduino was the one we chose to go with.
        This is because of the ease of programming and the low cost.
        We, for this project, didn't need to do more than RTTY (Radioteletype) to send back information such as the latitude and longitude.
        In future projects, we hope to send back pictures and this would require a more complex system.</p>

        <p><b>[TO BE CONTINUED]</b></p>
    </div>

<?php echo_end(); ?>
