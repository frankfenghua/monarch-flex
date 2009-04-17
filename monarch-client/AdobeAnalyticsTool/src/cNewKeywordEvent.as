package
{
	import flash.events.Event;

	public class cNewKeywordEvent extends Event
	{
		public function cNewKeywordEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		public var mKeywordEntry:cKeywordEntry = null;
		
	}
}