// Interactive 2D interpolating cubic B-spline, Evgeny Demidov  21 August 2001
import java.awt.*;
import java.awt.event.*;
import java.util.StringTokenizer;

public class Bint extends java.applet.Applet
  implements MouseMotionListener, MouseListener{
Image buffImage;          Graphics buffGraphics;
int n = 3, n1,  w,h,h1;
double[] Px,Py, dx,dy, Ax,Ay, Bi, B0,B1,B2,B3;

public void findCPoints(){
  Bi[1] = -.25;
  Ax[1] = (Px[2] - Px[0] - dx[0])/4;   Ay[1] = (Py[2] - Py[0] - dy[0])/4;
  for (int i = 2; i < n-1; i++){
   Bi[i] = -1/(4 + Bi[i-1]);
   Ax[i] = -(Px[i+1] - Px[i-1] - Ax[i-1])*Bi[i];
   Ay[i] = -(Py[i+1] - Py[i-1] - Ay[i-1])*Bi[i]; }
  for (int i = n-2; i > 0; i--){
   dx[i] = Ax[i] + dx[i+1]*Bi[i];  dy[i] = Ay[i] + dy[i+1]*Bi[i]; }
}

public void init() {
  w = Integer.parseInt(getParameter("width"));
  h = Integer.parseInt(getParameter("height"));  h1 = h-1;
  String s = getParameter("N"); if (s != null) n = Integer.parseInt(s);
  n1 = n+1;
  Px = new double[n1];  Py = new double[n1];
  dx = new double[n1];  dy = new double[n1];
  s=getParameter("pts");
  if (s != null){
   StringTokenizer st = new StringTokenizer(s);
   for (int i = 0; i < n; i++){
    Px[i] = w*Double.valueOf(st.nextToken()).doubleValue();
    Py[i] = h*Double.valueOf(st.nextToken()).doubleValue();}
    dx[0] = w*Double.valueOf(st.nextToken()).doubleValue();
    dy[0] = h*Double.valueOf(st.nextToken()).doubleValue();
    dx[n-1] = w*Double.valueOf(st.nextToken()).doubleValue();
    dy[n-1] = h*Double.valueOf(st.nextToken()).doubleValue(); }
  Ax = new double[n1];  Ay = new double[n1];
  Bi = new double[n1];
  B0 = new double[26];  B1 = new double[26];  B2 = new double[26];
  B3 = new double[26];
  double t = 0;
  for (int i= 0; i< 26; i++){
   double t1 = 1-t, t12 = t1*t1, t2 = t*t;
   B0[i] = t1*t12; B1[i] = 3*t*t12; B2[i] = 3*t2*t1; B3[i] = t*t2;
   t += .04;}
  buffImage = createImage(w, h);
  buffGraphics = buffImage.getGraphics();
  setBackground(Color.white);
  buffGraphics.clearRect(0,0, w, h);
  addMouseMotionListener(this);  addMouseListener(this);
  drawSpline();
}

public void destroy(){
 removeMouseMotionListener(this);
 removeMouseListener(this);
}
public void mouseMoved(MouseEvent e){}  //1.1 event handling
public void mouseClicked(MouseEvent e){}
public void mouseEntered(MouseEvent e){}
public void mouseExited(MouseEvent  e){}
public void mouseReleased(MouseEvent e){}

public void mousePressed(MouseEvent e){
  int y = h1 - e.getY();
  int x = e.getX();
  if ( e.isControlDown() ){
   int iMin = getPoint(x, y);
   for (int i = iMin; i < n; i++){
    Px[i] = Px[i+1];  Py[i] = Py[i+1];}
   dx[n-2] = dx[n-1];  dy[n-2] = dy[n-1];
   n1--; n--; }
  if ( e.isShiftDown() ){
   int iMin = getPoint(x, y) + 1;
   n1++;
   double[] px = new double[n1],  py = new double[n1];
   for (int i = 0; i < iMin; i++){
    px[i] = Px[i];  py[i] = Py[i];}
   for (int i = iMin; i < n; i++){
    px[i+1] = Px[i];  py[i+1] = Py[i];}
   Px = px;  Py = py;
   Px[iMin] = x;  Py[iMin] = y;
   double[] tx = new double[n1],  ty = new double[n1];
   tx[0] = dx[0];  ty[0] = dy[0];
   tx[n] = dx[n-1];  ty[n] = dy[n-1];
   dx = tx;  dy = ty;
   n++;
   Ax = new double[n1];  Ay = new double[n1];
   Bi = new double[n1]; }
  drawSpline();
  repaint();
}

public int getPoint(int x, int y){
  int iMin = 0;
  double Rmin = 1e10, r2,xi,yi;
  for (int i = 0; i < n; i++){
   xi = (x - Px[i]); yi = (y - Py[i]);
   r2 = xi*xi + yi*yi;
   if ( r2 < Rmin ){ iMin = i; Rmin = r2;}}
  return iMin;
}

public void mouseDragged(MouseEvent e) {
  int y = h1 - e.getY();  if (y < 0) y = 0;  if (y > h1) y = h1;
  int x = e.getX();  if ( x > w) x = w;  if (x < 0) x = 0;
  int iMin = 0;
  double Rmin = 1e10, r2,xi,yi, r3;
  for (int i = 0; i < n; i++){
   xi = (x - Px[i]); yi = (y - Py[i]);
   r2 = xi*xi + yi*yi;
   if ( r2 < Rmin ){ iMin = i; Rmin = r2;}}
  xi = (x - Px[0] - dx[0]); yi = (y - Py[0] - dy[0]);
  r2 = xi*xi + yi*yi;
  xi = (x - Px[n-1] + dx[n-1]); yi = (y - Py[n-1] + dy[n-1]);
  r3 = xi*xi + yi*yi;
  if ( (r2 < Rmin)||(r3 < Rmin) ){
   if ( r3 > r2 ){
    dx[0] = x - Px[0];  dy[0] = y - Py[0]; }
   else{
    dx[n-1] = Px[n-1] - x;  dy[n-1] = Py[n-1] - y; }}
  else{
   Px[iMin] = x; Py[iMin] = y;}
  drawSpline();
  repaint();
}

public void drawSpline(){
  findCPoints();
  int X,Y;
  buffGraphics.clearRect(0,0, w, h);
  buffGraphics.setColor(Color.blue);
  for (int i = 0; i < n; i++){
   X = (int)Px[i];  Y = h1-(int)Py[i];
   buffGraphics.drawRect(X-1,Y-1, 3,3);}
  buffGraphics.drawRect((int)(Px[0]+dx[0])-1,h1-(int)(Py[0]+dy[0])-1, 3,3);
  buffGraphics.drawRect((int)(Px[n-1]-dx[n-1])-1,h1-(int)(Py[n-1]-dy[n-1])-1,
   3,3);

  int Xo = (int)Px[0], Yo = h1-(int)Py[0],  Xold = Xo, Yold = Yo;
  for (int i = 1; i < n; i++){
   X = (int)(Px[i-1]+dx[i-1]);  Y = h1-(int)(Py[i-1]+dy[i-1]);
   buffGraphics.drawLine(Xo,Yo, X,Y);
   Xo = X;  Yo = Y;
   X = (int)(Px[i]-dx[i]);  Y = h1-(int)(Py[i]-dy[i]);
   buffGraphics.drawLine(Xo,Yo, X,Y);
   Xo = X;  Yo = Y;
   X = (int)Px[i];  Y = h1-(int)Py[i];
   buffGraphics.drawLine(Xo,Yo, X,Y);
   Xo = X;  Yo = Y;
  }

  buffGraphics.setColor(Color.red);
  for (int i = 0; i < n-1; i++){
   for (int k = 0; k < 26; k++){
    X = (int)(Px[i]*B0[k] + (Px[i]+dx[i])*B1[k] +
      (Px[i+1]-dx[i+1])*B2[k] + Px[i+1]*B3[k]);
    Y = h1-(int)(Py[i]*B0[k] + (Py[i]+dy[i])*B1[k] +
      (Py[i+1]-dy[i+1])*B2[k] + Py[i+1]*B3[k]);
    buffGraphics.drawLine(Xold,Yold, X,Y );
    Xold = X; Yold = Y;}
  }
}

public void paint(Graphics g) {
  g.drawImage(buffImage, 0, 0, this);
}

public void update(Graphics g){ paint(g); }

}