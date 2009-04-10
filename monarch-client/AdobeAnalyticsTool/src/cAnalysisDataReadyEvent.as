package
{
	import flash.events.Event;

	public class cAnalysisDataReadyEvent extends Event
	{
		public function cAnalysisDataReadyEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		public var mAnalysisParser:cAnalyticsParser;
		
	}
}