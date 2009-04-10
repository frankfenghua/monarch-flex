package
{
	import flash.events.Event;

	public class cStatOptionSelectedEvent extends Event
	{
		public function cStatOptionSelectedEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//						   DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// used to store the string title of the stat
		public var mTitle:String = "";
		// used to store the display name of the stat
		public var mDisplayName:String = "";
		// the viewing time span for the stat
		public var mTimeSpan:int = -1;
		
		public var mCountWeight:Number = 1;
		public var mGoodnessWeight:Number = 1;
		public var mEnglishProficiency:Number = 1;
		
		// time span TYPES
		public var TIME_SPAN_1_WEEK:int = 100;
		public var TIME_SPAN_2_WEEKS:int = 101;
		public var TIME_SPAN_1_MONTH:int = 102;
		public var TIME_SPAN_6_MONTHS:int = 103;
		public var TIME_SPAN_9_MONTHS:int = 104;
		public var TIME_SPAN_1_YEAR:int = 105;
	}
}