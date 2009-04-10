package
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;

	public class cLoggedinEvent extends Event
	{
		public function cLoggedinEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}	
		
		public var mFullName:String;
		public var mEmail:String;
		public var mPassword:String;
		public var mUserId:int;
		
		public var mCommunities:ArrayCollection = new ArrayCollection();
	}
}