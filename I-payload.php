<?php require('php/include.php');
echo_start(3, "ODIN-I Payload");
?>

<div class="container">
    <div class="page-header">
        <h1>Payload Design</h1>
    </div>
    <p>The payload is the part of the launch that does stuff. This diagram shows the basic idea:</p>
    <div class="well text-center"><img src="https://s4.postimg.org/4d9ec0avh/asdfasdferterte.png" class="img"></div>
    <p>
        It will be responsible for collecting data and taking photos.
        It will need to be designed in such a way as to be light, durable, reliable, insulative and moderately water resistant.
    </p>
    <h3>The materials</h3>
    <p>
        I went for Expanded polystyrene. 
        This is because it is light and water, proof and thermally resistant. 
        It is also cheap and easy to buy; the sheets I bought were intended for use in loft insulation. 
        To cut it I used a hack saw and to make indents fit the parts I used a Dremel (hand held drill). 
        I found this to be an effective way to do it but it may be worth considering making a hot wire tool.
    </p>
    <h3>The antenna</h3>
    <p>
        This may seem a little out of place here but the antenna is the element that defined the size of my payload.
        I am planning to use a ground plane antenna.
        These are effective antennas which fit well onto the payload and will radiate downward.
    </p>
    <div class="well text-center"><img src="https://s21.postimg.org/jd4m6n76f/adfsasdffdaagega.png" class="img"></div>
    <p>
        To make this antenna the correct size we need to make the radials and the active element 1/4 &lambda;.
        \[ v = f\lambda \]
        \[ f = 434.3\times 10^{6} \text{ Hz}\]
        (Frequency of the radio)
        \[ v = 2.8 \times 10^8 \text{ ms}^{-1}\]
        (Speed of electricity in copper)
        \[ \frac{\lambda}{4} = \frac{2.8 \times 10^8}{434.3 \times 10^6 \times 4} = 0.161 \text{m (3SF)}\]
        So 16.1 cm is to be the length of the element and the radials.
    </p>
    <div class="well text-center"><img src="https://s15.postimg.org/n6sdotyej/atarywetesdf.png" class="img"></div>
    <h3>Some photographs</h3>
    <div class="well text-center"><img src="https://s11.postimg.io/dapeqdvhv/DSC4662.jpg" class="img"><br />
    <img src="https://s11.postimg.io/zb5r70e5v/DSC4661.jpg" class="img"></div>
    <p>
        This worked, and to fix it togeather (and to the balloon) I used long screws and ducktape.
        In flight, the antenna will hang with the element pointing downwards.
        This is because of the radiation pattern of ground planes, having it face downwards should radiate power towards earth.
    </p>
</div>

<?php echo_end(); ?>