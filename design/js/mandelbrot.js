float xmin = -2.5;   
float ymin = -2;   
float wh = 4; 

void setup() {
  size(250,250);
}

void draw() {  
  
  loadPixels();  
    
  // Maximum number of iterations for each point on the complex plane  
  int maxiterations = 200;  
  
  // x goes from xmin to xmax  
  float xmax = xmin + wh;  
  // y goes from ymin to ymax  
  float ymax = ymin + wh;  
    
  // Calculate amount we increment x,y for each pixel  
  float dx = (xmax - xmin) / (width);  
  float dy = (ymax - ymin) / (height);  
  
  // Start y  
  float y = ymin;  
  for(int j = 0; j < height; j++) {  
    // Start x  
    float x = xmin;  
    for(int i = 0;  i < width; i++) {  
        
      // Now we test, as we iterate z = z^2 + cm does z tend towards infinity?  
      float a = x;  
      float b = y;  
      int n = 0;  
      while (n < maxiterations) {  
        float aa = a * a;  
        float bb = b * b;  
        float twoab = 2.0 * a * b;  
        a = aa - bb + x;  
        b = twoab + y;  
        // Infinty in our finite world is simple, let's just consider it 16  
        if(aa + bb > 320.0f) {  
          break;  // Bail  
        }  
        n++;  
      }  
        
      // We color each pixel based on how long it takes to get to infinity  
      // If we never got there, let's pick the color black  
      if (n == maxiterations) pixels[i+j*width] = color(0,0,((x*x+y*y)*10000) % 255);  
      else pixels[i+j*width] = color(n*255 % 255,n*5 % 255,n*25 % 255);  // Gosh, we could make fancy colors here if we wanted  
      x += dx;  
    }  
    y += dy;  
  }  
  updatePixels();
 text("Hello web!",20,20);
}  