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
		// collection of communities which the user created
		public var mCommunities:ArrayCollection = new ArrayCollection();
		// collection of communities which the user did NOT create
		public var mOtherCommunities:ArrayCollection = new ArrayCollection();
	}
}