package
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;

	public class cNewWebsitesEvent extends Event
	{
		public function cNewWebsitesEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		//collection of cWebsiteEntry;
		public var mWebsites:ArrayCollection = new ArrayCollection();
		
	}
}