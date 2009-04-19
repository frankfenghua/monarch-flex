package
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;

	public class cCommGroupGraphicClickedEvent extends Event
	{
		public function cCommGroupGraphicClickedEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//								DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// name of the community group
		public var mCommGroupName:String = "";
		// community group id
		public var mCommGroupId:int = 0;
		// community group creator
		public var mCreator:String = "";
		// community group created time
		public var mTime:String = "";
		// community group websites
		public var mWebsites:ArrayCollection = null;
		// community group keywords
		public var mKeywords:ArrayCollection = null;
		// type : either MY_COMMUNITY or OTHER_COMMUNITY
		public var mCanvasType:String = "";
	}
}