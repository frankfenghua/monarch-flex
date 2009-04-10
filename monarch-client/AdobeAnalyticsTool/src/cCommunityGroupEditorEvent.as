package
{
	import flash.events.Event;
	
	import mx.collections.ArrayCollection;

	public class cCommunityGroupEditorEvent extends Event
	{
		public function cCommunityGroupEditorEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		///////////////////////////////////////////////////////////////////////////////////
		//
		//						   DATA MEMBERS
		//
		///////////////////////////////////////////////////////////////////////////////////
		
		// community group name
		public var mCommuniutyGroupName:String = "";
		// community group id
		public var mCommunityGroupId:int = -1;
		// community group keywords
		public var mKeywords:ArrayCollection = null;
		// community group websites
		public var mWebsites:ArrayCollection = null;
		
	}
}