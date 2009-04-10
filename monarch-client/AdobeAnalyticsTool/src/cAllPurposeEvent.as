package
{
	import flash.events.Event;

	public class cAllPurposeEvent extends Event
	{
		public function cAllPurposeEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//								DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		public var mName:String = "";
		// name of the website selected
		public var mWebsiteName:String = "";
		// community group id
		public var mCommGroupId:int = -1;
		// website id
		public var mWebsiteId:int = -1;
		// website URL
		public var mWebsiteURL:String = "";
		// website type
		public var mWebsiteType:String = "";
		// website create time (Unix time stamp)
		public var mWebsiteCreatedTime:String = "";
		
	}
}