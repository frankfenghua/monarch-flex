/**
 * cKeywordEntry
 * Created by : Mariusz Choroszy
 * 
 * 
 * 
 */ 

package
{
	public class cKeywordEntry
	{
		public function cKeywordEntry(id:int, name:String, color:uint, commId:int)
		{
			mId = id;
			mName = name;
			mColor = color;
			mCommunityGroupId = commId;
		}

		/** The keyword databse id number **/
		public var mId:int;
		public var mName:String;	
		
		/** color for the legend **/
		public var mColor:uint = 0xFFFFFF;	
		
		/** community group id **/
		public var mCommunityGroupId:int = -1;

	}
}