package
{
	import flash.events.Event;

	public class cNewWebsiteEvent extends Event
	{
		public function cNewWebsiteEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		public var mWebsiteEntry:cWebsiteEntry;
		
	}
}