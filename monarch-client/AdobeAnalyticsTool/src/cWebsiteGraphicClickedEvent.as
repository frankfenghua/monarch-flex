package
{
	import flash.events.Event;

	public class cWebsiteGraphicClickedEvent extends Event
	{
		public function cWebsiteGraphicClickedEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//								DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// name of the website selected
		public var mWebsiteName:String = "";
		// community group id
		public var mCommGroupId:int = 0;
		// website URL
		public var mWebsiteURL:String = "";
		// website type
		public var mWebsiteType:String = "";
		// website create time (Unix time stamp)
		public var mWebsiteCreatedTime:String = "";
		
	}
}