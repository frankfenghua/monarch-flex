package
{
	import flash.events.Event;

	public class cNewCommunityEvent extends Event
	{
		public function cNewCommunityEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		public var mCommunityGroupEntry:cCommunityGroupEntry;	
	}
}