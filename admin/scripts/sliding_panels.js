function AnimationFrame(left, top, width, height, time)
{
  this.Left = left;
  this.Top = top;
  this.Width = width;
  this.Height = height;
  this.Time = time;
 
  this.Copy = function(frame)
  {
    this.Left = frame.Left;
    this.Top = frame.Top;
    this.Width = frame.Width;
    this.Height = frame.Height;
    this.Time = frame.Time;
  }
 
  this.Apply = function(element)
  {
    element.style.left = Math.round(this.Left) + 'px';
    element.style.top = Math.round(this.Top) + 'px';
    element.style.width = Math.round(this.Width) + 'px';
    element.style.height = Math.round(this.Height) + 'px';
  }
}

function AnimationObject(element)
{
  if(typeof(element) == "string")
    element = document.getElementById(element);
 
  var frames = null; 
  var timeoutID = -1;
  var running = 0;
  var currentFI = 0;
  var currentData = null;
  var lastTick = -1;
  var callback = null;
 
  var prevDir = 0;
 
  this.AddFrame = function(frame)
  {
    frames.push(frame);
  }
 
  this.SetCallback = function(cb)
  {
    callback = cb;
  }
 
  this.ClearFrames = function()
  {
    if(running != 0)
      this.Stop();
    frames = new Array();
    frames.push(new AnimationFrame(0,0,0,0,0));
    frames[0].Time = 0;
    frames[0].Left = parseInt(element.style.left);
    frames[0].Top = parseInt(element.style.top);
    frames[0].Width = parseInt(element.style.width);
    frames[0].Height = parseInt(element.style.height);
    currentFI = 0;
    prevDir = 0;
    currentData = new AnimationFrame(0,0,0,0,0);   
  }
 
  this.ResetToStart = function()
  {
    if(running != 0)
      this.Stop();
    currentFI = 0;
    prevDir = 0;
    currentData = new AnimationFrame(0,0,0,0,0);
    frames[0].Apply(element);
  }
 
  this.ResetToEnd = function()
  {
    if(running != 0)
      this.Stop();
    currentFI = 0;
    prevDir = 0;
    currentData = new AnimationFrame(0,0,0,0,0);
    frames[frames.length - 1].Apply(element);
  }
 
  this.Stop = function()
  {
    if(running == 0)
      return;
    if(timeoutID != -1)
      clearTimeout(timeoutID);
    prevDir = running;
    running = 0;
  }
  function animate()
  {
    if(running == 0)
      return;
    var curTick = new Date().getTime();
    var tickCount = curTick - lastTick;
    lastTick = curTick;
   
    var timeLeft =
       frames[((running == -1) ? currentFI+1 : currentFI)].Time
       - currentData.Time;
   
    while(timeLeft <= tickCount)
    {
      currentData.Copy(frames[currentFI]);
      currentData.Time = 0;
      currentFI += running;
      if(currentFI>= frames.length || currentFI <0)
      {
        currentData.Apply(element);
        lastTick = -1;
        running = 0;
        prevDir = 0;
        if(callback != null)
          callback();
        return;
      }
      tickCount = tickCount - timeLeft;
      timeLeft =
        frames[((running == -1) ? currentFI+1 : currentFI)].Time
        - currentData.Time;
    }
   
    if(tickCount != 0)
    {
      currentData.Time += tickCount;
      var ratio = currentData.Time/
        frames[((running == -1) ? currentFI+1 : currentFI)].Time;

      currentData.Left = frames[currentFI-running].Left +
         (frames[currentFI].Left
         - frames[currentFI-running].Left)
         * ratio;

      currentData.Top = frames[currentFI-running].Top +
         (frames[currentFI].Top
         - frames[currentFI-running].Top)
         * ratio;
      currentData.Width = frames[currentFI-running].Width +
         (frames[currentFI].Width
         - frames[currentFI-running].Width)
         * ratio;

      currentData.Height = frames[currentFI-running].Height +
         (frames[currentFI].Height
         - frames[currentFI-running].Height)
         * ratio;
    }
   
    currentData.Apply(element);

    timeoutID = setTimeout(animate, 33);
  }
  this.RunForward = function()
  {
    if(running == 1)
      return;
    if(running == -1)
      this.Stop();
    if(frames.length == 1 || element == null)
      return; 
     
    lastTick = new Date().getTime();

    //Start from the begining
    if(prevDir == 0)
    {
      currentFI = 1;
      currentData.Time = 0;
      currentData.Left = parseInt(element.style.left);
      currentData.Top = parseInt(element.style.top);
      currentData.Width = parseInt(element.style.width);
      currentData.Height = parseInt(element.style.height);
      frames[0].Copy(currentData);
    }
    else if(prevDir != 1)
    {
      currentFI++;
      currentData.Time =
          frames[currentFI].Time - currentData.Time;
    }
     
    running = 1;
    animate();
  }
 
  this.RunBackward = function()
  {
    if(running == -1)
      return;
    if(running == 1)
      this.Stop();
    if(frames.length == 1 || element == null)
      return;
       
    lastTick = new Date().getTime();
   
    //Start from the end
    if(prevDir == 0)
    {
      currentFI = frames.length-2;
      currentData.Left = parseInt(element.style.left);
      currentData.Top = parseInt(element.style.top);
      currentData.Width = parseInt(element.style.width);
      currentData.Height = parseInt(element.style.height);
      currentData.Time = frames[frames.length-1].Time;
      frames[frames.length-1].Copy(currentData);
      currentData.Time = 0;
    }
    else if(prevDir != -1)
    {
      currentData.Time =
          frames[currentFI].Time - currentData.Time;
      currentFI--;
    }
     
    running = -1;
    animate();
  }
   
  
 
  this.ClearFrames();
}

var animationObject = new Array();
var barStatus = new Array();
var size = 400;

if( typeof( window.innerWidth ) == 'number' ) {
    size = window.innerHeight - 200;
} else if( document.documentElement && document.documentElement.clientHeight ) {
    size = document.documentElement.clientHeight - 200;
} else if (document.body.clientHeight) {
	size = document.body.clientHeight -200;
}

function runAnimation(i)
{
	animation = animationObject[i]
	if(barStatus[i]) {
		animation.RunBackward();
		barStatus[i] = false;
	} else {
		for(a = 0; a < animationObject.length; a++) {
			if (barStatus[a]) {
				animationObject[a].RunBackward();
				barStatus[a] = false;
			}
		}
		animation.RunForward();
		barStatus[i] = true;
	}
}
