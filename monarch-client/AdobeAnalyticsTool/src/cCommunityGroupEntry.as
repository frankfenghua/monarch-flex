/**
 * 	cCommunityGroupEntry.as
 * 	Created by : Mariusz Choroszy
 * 	
 * 	This class represents one community entry.
 * 
 */

package
{
	import mx.collections.ArrayCollection;
	
	public class cCommunityGroupEntry
	{
		/**
		 * cCommunityGroupEntry
		 * 
		 */ 
		public function cCommunityGroupEntry(id:int, 
		 									 name:String, 
		 									 time:int,
		 									 creator:String,
		 									 websites:ArrayCollection,
		 									 keywords:ArrayCollection)
		{
			mId = id;
			mName = name;
			mCreatedTime = time;
			mCommunityGroupCreator = creator;
			mWebsites = websites;
			mKeywords = keywords;
		}
		
		/** The community database id **/
		public var mId:int;
		/** The community name **/
		public var mName:String;
		/** The community group created time (Unix time stamp) **/
		public var mCreatedTime:int;
		/** The collection of websites under this community **/
		public var mWebsites:ArrayCollection = new ArrayCollection();
		/** The collection of keywords for this community **/
		public var mKeywords:ArrayCollection = new ArrayCollection();
		/** The creator of the community group **/
		public var mCommunityGroupCreator:String = "";
				
		/** Used to track if the entry was just created or not **/
		public var mJustCreated:Boolean = false;

	}
}