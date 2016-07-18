<?php require('php/include.php');
echo_start(3, "ODIN-I Data");
?>

<div class="container">
    <div class="page-header">
        <h1>Data Logging</h1>
    </div>
        Gathering and logging data seems like a great idea.
        This will give me interesting information about conditions at high altitudes and how the balloon flight went.
        The information stored on the balloon can be far more detailed than the information transmitted because we can take readings every half second rather than every 10-15 seconds.
        However, the recovery of this information depends on of the recovery of the payload, which cannot be guaranteed.

        <h3>The Hardware</h3>

        <p>To log data we need storage.
        We could use the onboard EEPROM, however, this limits us to only 1024 bytes (and, by extension, 1024 characters).
        It is obvious that we will need more space to log our data.
        The  solution is to use flash storage.
        SD cards are a cheap, light, small and well-supported standard so is the method I will be using to store data.
        I got  <a href="http://www.ebay.co.uk/itm/182050142787">this</a> SD breakout for next to nothing.
        We communicate with the SD card using the SPI (Serial Peripheral Interface) protocol and, luckily, there are powerful libraries to abstract us from the low-level protocol and just worry about manage files on the SD card.</p>

        <p>We need something to log to test our datalogger so I will be using a DHT11 module (this is inappropriate for actual use in a balloon and will be using a DHT22 for the real thing).
        This gives us rough readings on temperature and humidity to log.</p>

        <h3>The Code</h3>
        <p>This code polls the DHT 11 module every 5 seconds and saves the data to the SD card.
        I am using the CSV standard to save the data in a spreadsheet. 
        I have not included the circuit design here as I am planning to do a comprehensive one when I have finished the payload.
        The CS(Slave Select pin) is connected to pin 4, the MISO(Master Input, Slave Output) pin goes to pin 13, the MOSI(Master Output, Slave Input) pin goes to pin 11, the SCK(Serial ClocK) goes to pin 12, and the DHT11 S pin goes to pin 7.</p>
        
<pre class="prettyprint ">
#include &lt;dht.h&gt;
#include &lt;SPI.h&gt;
#include &lt;SD.h&gt;

dht DHT; //Construct instances of the dht class

//Defining the pins for the SPI cs on the SD card and the DHT11 s pin.
#define DHT11_PIN 7
#define CHIP_SEL 4


String dataString; //Setting up the dataString string for the saveRow() function



void setup(){

  SD.begin(CHIP_SEL); //Open up communication with the SD card

  //If the file datalog.csv does not exist then set up the headings for the spreadsheet
  if (!SD.exists("datalog.csv")){
    dataString = "Time,Temp,Humidity";
    saveRow();
  }
  
}

void loop()
{
  DHT.read11(DHT11_PIN); //get data from the DHT11 sensor

  //Construct the datastring formatted for CSV
  dataString = millis();
  dataString += ",";
  dataString += DHT.temperature;
  dataString += ",";
  dataString += DHT.humidity;
  
  saveRow(); //Calls the saveRow() function to write the dataString to the SD card
  delay(5000);
}

void saveRow(){
  File dataFile = SD.open("datalog.csv", FILE_WRITE); // Open datalog.csv the file in WRITE mode
  if (dataFile) {
    dataFile.println(dataString); //Appends the dataString to the end of the file
    dataFile.close(); //Closes the file
  }
}
</pre>
       <h3>DHT11</h3>
        <p>Measuring temperature and humidity with the DHT11 is an inaccurate solution (±2°C and  ±5%) and would not work in a high altitude balloon.
        This is because the range of temperatures it measures is 0-50°C.
        In the balloon launch, I will be using the very similar DHT22 module.
        This is more accurate (±0.5°C and ±2.5%) and, more crucially, has a temperature range of -40-125°C.</p>
</div>

<?php echo_end(); ?>