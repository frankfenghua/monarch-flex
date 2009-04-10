package
{
	import flash.events.Event;

	public class cCreateAccountEvent extends Event
	{
		public var CREATE_ACCOUNT:String = "createAccount";
		public var CANCEL:String = "cancel";
		
		public function cCreateAccountEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////
		//
		//									DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////////////
		
		public var mType:String = "";
		public var mFullName:String = "";
		public var mEmail:String = "";
		public var mPassword:String = "";
		
	}
}