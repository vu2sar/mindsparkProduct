/*  This generalised timer takes the following inputs :

To use hte timer Call CreateTimer() method in your html.
Eg : CreateTimer('MyTimer',600,1,0,0,0);

Note :
	--> The timer will not work if the value for H,M and S are set to 111 respectively.Also you cannot leave any of the values blank.
	--> Place the peice of code under "window.load" as at the time the CreateTimer is loaded , it needs to have the ELEMENT loaded .

1)	TimerId : The Id of the ELEMENT where you want to display the timer 

2)	Time : Total number of SECONDS you wnat the timer to run for 

3)	H : Hours --> can be 0 or 1 .		
	
		0 = Display
   		1 = Dont Display
				
4)	M : Minutes --> can be 0 or 1 . 	
				
		0 = Display
   		1 = Dont Display

5)	S : Seconds --> can be 0 or 1 .

		0 = Display
   		1 = Dont Display

6)	D : Ascending or Descendidng : can be 0 or 1 . 
	
		0 = Descending [10 to 0]
   		1 = Ascending [0 to 10]
													

Devloped by : janak shah [8 june 2012]
Editted By : 
*/
//========================================  Variable Declarations ================================
var Sec=0;
var Min=0;
var Hour=0;
var IsHour;
var IsMin;
var IsSec;
var IsAsc;
var finished=0;
var TotalSeconds;
//========================================  CreateTimer  ======================================
	function CreateTimer(TimerID,Time,H,M,S,D)
	{
			IsHour=H;
			IsMin=M;
			IsSec=S;
			IsAsc=D;
			Timer = document.getElementById(TimerID);
			TotalSeconds = Time;
			if(IsAsc==0)
			{
				CountDown();
			}
			else 
			{
				StartTimer();
			}
			window.setTimeout("Tick()", 1000);
	}
//========================================  CheckFinish =========================================
	function CheckFinish(S,M,H)
	{
		var tot=(S+(H*60)+(M*60));
		if(tot<TotalSeconds)  // if(s<TotalSeconds && stop==0 )  Means stop button is not called
		{
			return;
		}
		else
		{
			alert("Time's Up . Time taken was : " + LeadingZero(H) + ":" + LeadingZero(M) + ":" +  LeadingZero(S));
			finished=1;
		}
	}
//========================================  StartTimer  =========================================
	function StartTimer() 
	{	
	var TimeStr;
		Sec+=1;
		if(Sec==60)
		{
			if(Min==60)
			{
					Hour+=1;
					Min=0;
			}
			else
			{
					Min+=1;
			}
		}
		CheckFinish(Sec,Min,Hour);
		
		if(IsHour==0)
		{
			if(IsMin==0)
			{
				if(IsSec==0)
				{
					 TimeStr = LeadingZero(Hour) + ":" + LeadingZero(Min) + ":" + LeadingZero(Sec);
				}
				else if(IsSec==1)
				{
					 TimeStr = LeadingZero(Hour) + ":" + LeadingZero(Min) ;
				}
			}
			else if(IsMin==1)
			{
				if(IsSec==0)
				{
					 TimeStr = LeadingZero(Hour) + ":" + LeadingZero(Sec);
				}
				else if(IsSec==1)
				{
					 TimeStr = LeadingZero(Hour);
				}
			}
		}
		else if(IsHour==1)
		{
			if(IsMin==0)
			{
				if(IsSec==0)
				{
					 TimeStr = LeadingZero(Min) + ":" + LeadingZero(Sec);
				}
				else if(IsSec==1)
				{
					 TimeStr = LeadingZero(Min) ;
				}
			}
			else
			{
				if(IsSec==0)
				{
					 TimeStr = LeadingZero(Sec);
				}
			}
		}
		Timer.innerHTML = TimeStr;
	}
//========================================  CountDown  ========================================
	function CountDown() 
	{	
		var Seconds = TotalSeconds;
		var Minutes = Math.floor(Seconds / 60);
		Hour = Math.floor(Minutes / 60);
		if(IsHour==1 && IsMin==0 && IsSec==0)
		{
				Seconds -= Minutes * (60);
				var TimeStr = LeadingZero(Minutes) + ":" + LeadingZero(Seconds);
		}
		else if(IsHour==0 && IsMin==0 && IsSec==0)
		{
				Seconds -= Minutes * (60);
				Minutes -= Hour * (60);
				var TimeStr = LeadingZero(Hour) + ":" + LeadingZero(Minutes) + ":" + LeadingZero(Seconds);
		}
		else if(IsHour==1 && IsMin==1 && IsSec==0)
		{
				var TimeStr =  LeadingZero(Seconds);
		}
		else if(IsHour==1 && IsMin==0 && IsSec==1)
		{
				Seconds -= Minutes * (60);
				var TimeStr = LeadingZero(Minutes); 
		}
		else if(IsHour==0 && IsMin==1 && IsSec==0)
		{
				Seconds -= Minutes * (60);
				Minutes -= Hour * (60);
				var TimeStr = LeadingZero(Hour) + ":" + LeadingZero(Seconds);
		}
		else if(IsHour==0 && IsMin==1 && IsSec==1)
		{
				Seconds -= Minutes * (60);
				Minutes -= Hour * (60);
				var TimeStr = LeadingZero(Hour);
		}
		else if(IsHour==0 && IsMin==0 && IsSec==1)
		{
				Seconds -= Minutes * (60);
				Minutes -= Hour * (60);
				var TimeStr = LeadingZero(Hour) + ":" + LeadingZero(Minutes);
		}
		Timer.innerHTML = TimeStr;
	}
//========================================  LeadingZero ============================================	
	function LeadingZero(Time)
	{
		return (Time < 10) ? "0" + Time : + Time;
	}
//========================================  Tick  =====================================================	
//checks @ every second about the time 	
	function Tick() 
	{
		if (TotalSeconds <= 0 )
		{
				alert("Time's Up !! ");
				return;
		}
		if(IsAsc==0)
		{
			alert('shsjdkhjdksfs');
			TotalSeconds -= 1;
			CountDown();
		}
		else if(IsAsc==1 && finished==0)
		{
			StartTimer();
		}
		window.setTimeout("Tick()", 1000);
	}
	
