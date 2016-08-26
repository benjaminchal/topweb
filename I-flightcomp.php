<?php require('php/include.php');
echo_start(3, "ODIN-I computer");
?>

<div class="container">
    <div class="page-header">
        <h1>The flight computer</h1>
    </div>
    <h3>The hardware</h3>
    <p>
        Finally, all of the bits and pieces arrived (postage from china is annoyingly slow). 
        We can now construct a robust and reliable tracking beacon flight computer. 
        The primary function of this device will be to broadcast its location to earth so that I can find the payload when it lands. 
        This is achieved through the use of a uBlox GPS module and a <a href="https://store.uputronics.com/index.php?route=product/product&product_id=60">434MHz ISM transmitter</a>. 
        The microcontroller I am going to be using is the ATmega 328P. 
        The low specs of this chip required some neat tricks to achieve all I wanted it to do. 
        2K of ram is tiny in today's world and only having one hardware UART is very annoying. 
        The reason to use this is that the power usage is tiny. 
        This is critical as it will allow me to reduce weight when it comes to the batteries (I can get ~20 hours use out of 2 AAs*). 
        The SPI interface on the ATmega is used to allow for writing to an SD card. This allows for the creation of a detailed flight log if I can recover the payload.
    </p>
    <p>
        To supply power I am connecting 2 1.5v AA batteries to a 5V step-up converter.
        Then I am connecting the 5V out directly into the VCC and ground of the ATmega 328P breakout board.
        Because I don't entirely trust the cheap step-up converters I have I connected two in parallel so that if one fails I will be ok.
    </p>
    <p>
        The reasons behind my specific hardware choices came down mostly to price.
        Initially, I was going to do my own circuit for the ATmega 328P but I discovered that I could get a tiny breakout board for less than the cost of the chip plus the components required (~£2). 
        The GPS module was the cheapest uBlox serial breakout board was ~£8. 
        This was again cheaper than buying the uBlox chip on its own and even came with a ceramic antenna. The SD card breakout cost ~£3. 
        The most expensive part was the radio at a massive £21. 
        I purchased 2 DHT22 module for around £3 each but I broke one so I can now only measure the external temperature. 
        So for less than £40 I built the tracking system.
    </p>
        <div class="well text-center"><img src="https://s10.postimg.org/ojw9d7gih/asdfasdfrg.png" class="img"></div>
    <h3>The code</h3>
    <p>
        Coding this was a challenge. 
        This was mostly down to the amount of RAM available on the ATmega 328P. 2048 bytes of RAM quickly vanishes when dealing with what I want to do. 
        I needed to be very prudent about allocating memory to anything, and ended up makeing almost all variables global.
        The String.h library is flawed, it made the code unstable (due I think to an over allocation of memory) so if you are doing this please don't use this library, an array of chars works better.
        There are workarounds (you can write data to an EPROM and get an extra 1k for instance) but I managed without them.
        The String.h library is flawed, it made the code unstable (due I think to an over allocation of memory).
        So if you are doing this please don't use this library, an array of chars works better.
        It also became necessary to wire the GPS module into the hardware UART on the arduino.
        This is because software serial disables interrupt routines, which destroys the RTTY.
        Finally, the reason the DHT22 sensors are not used is because mine broke and I did not have time to replace them.
    </p>
    <p>
        The following is the final peice of code that will be used in the tracking becon.
        I commented it for you to read through.
    </p>
<pre class="prettyprint">
//Includes
#include &lt;avr/pgmspace.h&rt;
#include &lt;stdlib.h&rt;
#include &lt;util/crc16.h&rt;
#include &lt;avr/io.h&rt;
#include &lt;avr/interrupt.h&rt;
//#include &lt;dht.h&rt;
#include &lt;SPI.h&rt;
#include &lt;SD.h&rt;
#include &lt;TinyGPS_UBX.h&rt;
TinyGPS tGPS;


//DEFINE
#define DHT22_PIN 7
#define CHIP_SEL 4
#define RADIOPIN 9
#define BAUD_RATE 50 // change as required

//GLOBAL VARS

  // rtty innterupt vars
  volatile int tx_status = 0;
  volatile char *ptr = NULL;
  char send_datastring[102];
  char live_datastring[102];
  volatile boolean sentence_needed = true;
  char currentbyte;
  int currentbitcount;
  int tx_counter = 0;

  // gps vars
  char tLAT[11]; // The size is to be 11 bytes because this is the largest this string could be (-XXX.XXXXXX)
  char tLNG[11];
  char tALT[10];
  char tVAC[10];
  byte gps_hour, gps_minute, gps_second;
  long gps_lat, gps_lon;
  unsigned long gps_fix_age;
  byte gps_set_sucess = 0;

  // sd vars
  char log_dataString[50];

  // misc vars
  int flight_stage = 0;
  unsigned long int logTimer = 10000;

//RTTY CODE

  // RTTY Interrupt Routine (Thanks to Matthew Beckett of norb.co.uk)
  ISR(TIMER1_COMPA_vect){
    switch (tx_status){

      case 0: // when the next byte needs to be gotten
        if (ptr){
          currentbyte = *ptr; // read first byte where pointer is pointing too
          if (currentbyte){
            tx_status = 1;
            sentence_needed = false;
            // The lack of "break" in this branch means that we
            // fall through to "case 1" immediately, in order to start
            // sending the start bit.
          }
          else {
            sentence_needed = true;
            break;
          }
        }
        else {
          sentence_needed = true;
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
        break;

      }

  }

  // function t o toggle radio pin high and low as per the bit
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
    TCCR1B |= (1 &lt;&lt; WGM12);   // turn on CTC mode:
    // Set CS10 and CS12 bits for:
    TCCR1B |= (1 &lt;&lt; CS10);
    TCCR1B |= (1 &lt;&lt; CS12);
    // enable timer compare interrupt:
    TIMSK1 |= (1 &lt;&lt; OCIE1A);
    sei();          // enable global interrupts
  }

  // checksum gen by the UKHAS standards
  uint16_t gps_CRC16_checksum (char *strings) {
    size_t i;
    uint16_t crc;
    uint8_t c;

    crc = 0xFFFF;

    // Calculate checksum ignoring the first two $s
    for (i = 2; i &lt; strlen(strings); i++) {
      c = strings[i];
      crc = _crc_xmodem_update (crc, c);
    }

    return crc;
  }

  // set standard tx
  void setDataStr(char *s){
    //int temp = 25; //DHT.temperature

    strcpy(send_datastring ,s);
    unsigned int CHECKSUM = gps_CRC16_checksum(send_datastring); // Calculates the checksum for this datastring
    char checksum_str[6];
    sprintf(checksum_str, "*%04X\n", CHECKSUM);
    strcat(send_datastring,checksum_str);
    ptr = &amp;send_datastring[0];
    sentence_needed = false;
    //Serial.print("TX SET: "); &lt;-- Used for debugging
    //Serial.print(send_datastring);
  }

//SPI SD CARD

  // initilise sd card
  void sdINIT(){
    SD.begin(CHIP_SEL); //Open up communication with the SD card

    //If the file datalog.csv does not exist then set up the headings for the spreadsheet
    if (!SD.exists("datalog.csv")){
      snprintf(log_dataString,50, "Time,Latitude,Longditude,Altitude");
      saveRow();
    }
  }

  // construct and log datastring
  void logData(){
    //Construct the datastring formatted for CSV
    snprintf(log_dataString, 50,"%02d:%02d:%02d,%s,%s,%s",
    gps_hour, gps_minute, gps_second,
    tLAT, tLNG, tALT);
    saveRow();
  }

  // Save row to datastring (char log_datastring[])
  int saveRow(){
    File dataFile = SD.open("datalog.csv", FILE_WRITE); // Open datalog.csv the file in WRITE mode
    if (dataFile) {
      dataFile.println(log_dataString); //Appends the dataString to the end of the file
      dataFile.close(); //Closes the file
      return 1;
    }else{
      return 0;
    }
  }

//GPS SERIAL (You will need this library https://github.com/x-f/TinyGPS_UBX)

  // setup gps
  void setupGPS() {
    Serial.begin(9600);
    // switch baudrate to 4800 bps
    //GPS_Serial.println("$PUBX,41,1,0007,0003,4800,0*13");
    //GPS_Serial.begin(4800);
    //GPS_Serial.flush();

    delay(5000);
    uint8_t setNav[] = {0xB5, 0x62, 0x06, 0x24, 0x24, 0x00, 0xFF, 0xFF, 0x06, 0x03, 0x00, 0x00, 0x00, 0x00, 0x10, 0x27, 0x00, 0x00, 0x05, 0x00, 0xFA, 0x00, 0xFA, 0x00, 0x64, 0x00, 0x2C, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x16, 0xDC};
    sendUBX(setNav, sizeof(setNav)/sizeof(uint8_t));
    while(!gps_set_sucess)
    {
      sendUBX(setNav, sizeof(setNav)/sizeof(uint8_t));
      gps_set_sucess=getUBX_ACK(setNav);
    }
    gps_set_sucess=0;
    // turn off all NMEA sentences for the uBlox GPS module
    // ZDA, GLL, VTG, GSV, GSA, GGA, RMC
    Serial.println("$PUBX,40,ZDA,0,0,0,0*44");
    Serial.println("$PUBX,40,GLL,0,0,0,0*5C");
    Serial.println("$PUBX,40,VTG,0,0,0,0*5E");
    Serial.println("$PUBX,40,GSV,0,0,0,0*59");
    Serial.println("$PUBX,40,GSA,0,0,0,0*4E");
    Serial.println("$PUBX,40,GGA,0,0,0,0*5A");
    Serial.println("$PUBX,40,RMC,0,0,0,0*47");

    delay(500);
  }


  // send gps
  void sendUBX(uint8_t *MSG, uint8_t len) {
    for(int i=0; i&lt;len; i++) {
      Serial.write(MSG[i]);
    }
    Serial.println();
  }

  // Check ACK
  boolean getUBX_ACK(uint8_t *MSG) {
    uint8_t b;
    uint8_t ackByteID = 0;
    uint8_t ackPacket[10];
    unsigned long startTime = millis();
    //Serial.print(" * Reading ACK response: ");

    // Construct the expected ACK packet
    ackPacket[0] = 0xB5;  // header
    ackPacket[1] = 0x62;  // header
    ackPacket[2] = 0x05;  // class
    ackPacket[3] = 0x01;  // id
    ackPacket[4] = 0x02;  // length
    ackPacket[5] = 0x00;
    ackPacket[6] = MSG[2];  // ACK class
    ackPacket[7] = MSG[3];  // ACK id
    ackPacket[8] = 0;   // CK_A
    ackPacket[9] = 0;   // CK_B

    // Calculate the checksums
    for (uint8_t i=2; i&lt;8; i++) {
      ackPacket[8] = ackPacket[8] + ackPacket[i];
      ackPacket[9] = ackPacket[9] + ackPacket[8];
    }

    while (1) {

      // Test for success
      if (ackByteID &rt; 9) {
        // All packets in order!
        //Serial.println(" (SUCCESS!)");
        return true;
      }

      // Timeout if no valid response in 3 seconds
      if (millis() - startTime > 3000) {
        //Serial.println(" (FAILED!)");
        return false;
      }

      // Make sure data is available to read
      if (Serial.available()) {
        b = Serial.read();

        // Check that bytes arrive in sequence as per expected ACK packet
        if (b == ackPacket[ackByteID]) {
          ackByteID++;
          //Serial.print(b, HEX);
        }
        else {
          ackByteID = 0;  // Reset and look again, invalid order
        }

      }
    }
  }

  // request uBlox to give fresh data
  boolean pollGPS() {
    //GPS_Serial.println("$PUBX,00*33");
    Serial.println("$PUBX,00*33");
    delay(300);
    unsigned long starttime = millis();
    while (true) {
      if (Serial.available()) {
        char c = Serial.read();
        if (tGPS.encode(c))
          return true;
      }
      // timeout
      if (millis() - starttime > 1000) {
        break;
      }
    }
    return false;
  }

  // string parse for outputs
  void gpsStrParse() {
    tGPS.crack_time(&amp;gps_hour, &amp;gps_minute, &amp;gps_second, &amp;gps_fix_age);
    tGPS.get_position(&amp;gps_lat, &amp;gps_lon, &amp;gps_fix_age);
    dtostrf(gps_lat/100000.0, 1, 6, tLAT);
    dtostrf(gps_lon/100000.0, 1, 6, tLNG);
    dtostrf(tGPS.altitude()/100.0, 1, 0, tALT);
    dtostrf(tGPS.vspeed()/100, 1, 2, tVAC);
  }

void setup() {
  pinMode(RADIOPIN, OUTPUT);
  setupGPS();
  sdINIT();
  initialise_interrupt();
}

void loop() {

    if(logTimer &lt; millis()){
      pollGPS();
    switch(flight_stage){
      case 0: //pre-flight mode
        if(tGPS.has_fix() == 0){
          snprintf(live_datastring,102,"FIX NOT FOUND");
          break;
        }else{
          flight_stage = 1;
          // no break so will follow on to case 1
        }
      case 1: //GPS co-ordinates found
        gpsStrParse();
        snprintf(live_datastring,102,"$$ODIN-I,%05d,%02d:%02d:%02d,%s,%s,%s,%s", tx_counter,
        gps_hour, gps_minute, gps_second,
        tLAT, tLNG, tALT, tVAC); // %f dosn't work on the arduino without alot of work (and memory), the gpsStrParse
      }
      //updateSen();
      logData();
      logTimer = millis() + 2000; // Will repeat roughly every 2 seconds
    }

  if(sentence_needed){
    setDataStr(live_datastring);
    tx_counter++;
  }
}

</pre>
</div>
<?php echo_end(); ?>
