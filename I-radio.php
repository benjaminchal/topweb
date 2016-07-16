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

        <h3>Circuit Design</h3>

        <p>To make the NTX2B transmit we take the EN pin high and use the TXD pin to 'modulate' the frequency. The TXD pin has an input from 0v-3v and this shifts the frequency by 5 KHz.</p>

        <p>For RTTY on SSB we need to have a 'high' frequency and a 'low' frequency. To achieve this we would need to have the ability to consistently alternate the voltage from low to high. The TXD pin has an internal resistance of 100 K&#8486;. This means that we can divide the 5v output from the Arduino into something far lower. The value of the resistor used would affect the 'carrier shift'. I went for a resistance of around 4 M&#8486; as this gave me a shift of around 200Hz. This resistance was worked out by the following:</p>

        <p>We know pin 7 (TXD) has a DC bias of 1.46 v (Measured by me).
        The datasheet implies an almost linear relationship between the voltage on the TXD and the frequency shift in the interval of \( [0,3] \) v and \( [0,5] \) KHz.
        Hence we can express \(v\) in terms of \(f\)  (where \(f\) is the frequency shift in Hz and \(v\) is the voltage change). \(v = 0.0006f\).
        This means that for a frequency shift of 200Hz we would need a voltage change of 0.12v.
        I need to divide the 5v output from the Arduino pin down to 0.12v.
        To work out the resistance needed we do the following (we know the internal resistance of the TXD pin is 0.1 M&#8486;)<p>
        
        \[ \frac{V_{IN}\times R_{2}}{R_{1}+R_{2}} = V_{out} \]
        
        \[\frac{5}{R+0.1}\times 0.1 = 0.12\]
        \[\Rightarrow R = 4.06667\]
        
        <p>In reality, the relationship isn't perfectly linear so, after experimenting, the actual resistance I used was 3.95 M&#8486;.
        This appeared to alternate the voltage across the TXD PIN from 1.45 (low) and 1.54 (high) which produced a frequency shift of 200Hz.</p>

        <p>Here was the testing code:</p>
        
        <!--<div class="well ">-->

        
<pre class="prettyprint ">void setup() {
  pinMode(9, OUTPUT); //Setting pin 9 to an output pin
}

void loop() {
  digitalWrite(9, HIGH); //Setting the pin high (+5v)
  delay(5000); //Pausing for 5 seconds
  digitalWrite(9, LOW); //Setting the pin low (0v)
  delay(5000); //Pausing for 5 seconds
}</pre>
        
        <!--</div>-->
        <p>And here is the circuit diagram:</p>
        <div class="well text-center"><img src="https://s31.postimg.org/xobgj66sr/NTX2_B_wireing_diagramme.png" class="img"></div>
        

        <p>Now we have built a circuit capable of giving us a high and a low frequency output we can get onto programming the Arduino to output RTTY.
        Here is an output from the arduino radio (using fldigi):</p>
        
        <div class="well text-center"><img src="https://s31.postimg.org/v5pripauj/Spectra_View.png" width="100%"></div>
        
        <h3>RTTY CODE</h3>
        <p>RTTY works by alternating between high and low to transmit binary data.
        We go from low (0) to high (1) to transmit 7 bit ASCII (a standard used to store text into binary).
        In C it is relatively simple to do this.
        We need to cycle through the bytes in string with a pointer and transmit each bit.
        In the code below this is managed with a switch statement.</p>

        <p>To allow us to do measurements and logging whilst the RTTY is transmitting we need to use timer interrupts.
        This timer interrupt must be set to operate at the baud rate.
        This is done in the initialise_interrupt() function in the following code.
        To find out more about timer interrupts please read the ATmega328p datasheet. I
        It works primarily by comparing the timer counter control register to the output compare register.
        The timer counter control register clears when it is found to be equal to the output compare register and this triggers the interrupt.</p>
        
        <p>So, to control the baud. The interrupt function transmits 1 bit each time it is run.
        This means that the number of times this function is called per second is the baud of the RTTY transmission.
        So we need to set the output compare register to a number such that the timer counter control register will reach it in 1/baud seconds.
        This is done by dividing the clock frequency by the pre-scaler and then dividing all of this by the desired baud.</p>
        
        <p>This is code adapted from <a href="https://ukhas.org.uk/guides:interrupt_driven_rtty">Matthew Beckett's</a> code from a UKHAS article:</p>
        
<pre class="prettyprint">
#include &lt;string.h&gt;
#include &lt;util/crc16.h&gt;
#include &lt;avr/io.h&gt;
#include &lt;avr/interrupt.h&gt;
 
#define RADIOPIN 9
#define LED_1 13
#define BAUD_RATE 50 // change as required
 
volatile int tx_status = 0;
volatile char *ptr = NULL;
char currentbyte;
int currentbitcount;
 
volatile boolean sentence_needed = true;
int tx_counter = 10000;
 
char send_datastring[102];
 
 
 
// RTTY Interrupt Routine
ISR(TIMER1_COMPA_vect){
  switch (tx_status){
 
    case 0: // when the next byte needs to be gotten
      if (ptr){
        currentbyte = *ptr; // read first byte where pointer is pointing too
        if (currentbyte){
          tx_status = 1;
          sentence_needed = false;
          digitalWrite(LED_1, LOW);
          // warning! The lack of "break" in this branch means that we
          // fall through to "case 1" immediately, in order to start
          // sending the start bit.
        }
        else {
          sentence_needed = true;
          digitalWrite(LED_1, HIGH);
          break;
        }
      }
      else {
        sentence_needed = true;
        digitalWrite(LED_1, HIGH);
        break;
      }
 
    case 1: // first bit about to be sent
      rtty_txbit(0); // send start bit
      tx_status = 2;
      currentbitcount = 1; // set bit count to 0 ready for incrementing to 7 for last bit of a ASCII-7 byte
      break;
 
    case 2: // normal status, transmitting bits of byte (including first and last)
 
      rtty_txbit(currentbyte &amp; 1); // send the currentb bit
 
      if (currentbitcount == 7){ // if we've just transmitted the final bit of the byte
        tx_status = 3;
      }
 
      currentbyte = currentbyte >> 1; // shift all bits in byte 1 to right so next bit is LSB
      currentbitcount++;
      break;
 
    case 3: // if all bits have been transmitted and we need to send the first of two stop bits
      rtty_txbit(1); // send first stop bit
      tx_status = 4;
      break;
 
    case 4: // ready to send the last of two stop bits
      rtty_txbit(1); // send the final stop bit
      ptr++; // increment the pointer for reading next byte in buffer
      tx_status = 0;
      Serial.println("");
      break;
 
    }
 
}
 
 
// function to toggle radio pin high and low as per the bit
void rtty_txbit (int bit)
{
 if (bit)
 {
 // high
 digitalWrite(RADIOPIN, HIGH);
 }
 else
 {
 // low
 digitalWrite(RADIOPIN, LOW);
 }
}  
 
 
 
void initialise_interrupt() 
{
  // initialize Timer1
  cli();          // disable global interrupts
  TCCR1A = 0;     // set entire TCCR1A register to 0
  TCCR1B = 0;     // same for TCCR1B
  OCR1A = F_CPU / 1024 / (BAUD_RATE - 1);  // set compare match register to desired timer count
  TCCR1B |= (1 &#60;&#60; WGM12);   // turn on CTC mode:
  // Set CS10 and CS12 bits for:
  TCCR1B |= (1 &#60;&#60; CS10);
  TCCR1B |= (1 &#60;&#60; CS12);
  // enable timer compare interrupt:
  TIMSK1 |= (1 &#60;&#60; OCIE1A);
  sei();          // enable global interrupts
}
 
 
 
void setup()
{
  pinMode(RADIOPIN, OUTPUT);
  initialise_interrupt();
  Serial.begin(9600);
}
 
void loop()
{
 // for you to have a play with ;-
 float timemill = millis();
 int timemin = timemill/60000;
 Serial.println(timemin);
  
 if(sentence_needed){
  snprintf(send_datastring,102,"$$test,%d,19:59:00,21.0000,12.4549,1000", tx_counter);
  tx_counter++;
  unsigned int CHECKSUM = gps_CRC16_checksum(send_datastring); // Calculates the checksum for this datastring
  char checksum_str[6];
  sprintf(checksum_str, "*%04X\n", CHECKSUM);
  strcat(send_datastring,checksum_str);
  ptr = &amp;send_datastring[0];
  sentence_needed = false;
 }
}

uint16_t gps_CRC16_checksum (char *string) {
  size_t i;
  uint16_t crc;
  uint8_t c;
 
  crc = 0xFFFF;
 
  // Calculate checksum ignoring the first two $s
  for (i = 2; i &#60; strlen(string); i++) {
    c = string[i];
    crc = _crc_xmodem_update (crc, c);
  }
 
  return crc;
}</pre>

        <p>And here is the output in DL-fldigi:</p>
       
       
        <div class="well text-center"><img src="https://s32.postimg.org/cknstxxrp/TEST_CODE_RUNNING.png" width="100%"></div>
    </div>
<?php echo_end(); ?>
