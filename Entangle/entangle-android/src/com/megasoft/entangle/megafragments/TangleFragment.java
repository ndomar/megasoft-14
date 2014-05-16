package com.megasoft.entangle.megafragments;

import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.LinearLayout;
import android.widget.SearchView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.HomeActivity;
import com.megasoft.entangle.R;
import com.megasoft.entangle.StreamRequestFragment;
import com.megasoft.requests.GetRequest;

public class TangleFragment extends Fragment {

	private HomeActivity activity;
	
	private String queryParameters;
	
	private View view;
	
	/**
	 * The domain to which the requests are sent
	 */
	private String rootResource = Config.API_BASE_URL_SERVER;

	/**
	 * The tangle id to which this stream belongs
	 */
	private int tangleId;

	/**
	 * The tangle name to which this stream belongs
	 */
	private String tangleName;

	/**
	 * The session id of the user
	 */
	private String sessionId;

	/**
	 * The FragmentTransaction that handles adding the fragments to the activity
	 */
	private FragmentTransaction transaction;

	/**
	 * The HashMap that contains the mapping of the user to its id
	 */
	private HashMap<String, Integer> userToId = new HashMap<String, Integer>();

	/**
	 * The HashMap that contains the mapping of the tag to its id
	 */
	private HashMap<String, Integer> tagToId = new HashMap<String, Integer>();

	/**
	 * This method is called when the activity starts , it sets the attributes
	 * and redirections of all the views in this activity
	 * 
	 * @param savedInstanceState
	 *            , is the passed bundle from the previous activity
	 */
	
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
	}
	
	@Override
    public View onCreateView(LayoutInflater inflater,
            ViewGroup container, Bundle savedInstanceState) {
        // The last two arguments ensure LayoutParams are inflated
        // properly.
         view = inflater.inflate(
        		 R.layout.activity_tangle, container, false);
         
//         ImageView filterButton = (ImageView) view.findViewById(R.id.filterButton);
//         filterButton.setOnClickListener(new View.OnClickListener() {
//			
//			@Override
//			public void onClick(View arg0) {
//				filterStream(arg0);
//				
//			}
//		});
         
         tangleId = getArguments().getInt("tangleId");
         tangleName = getArguments().getString("tangleName");
        
 		setSearchListener();
 		
        return view;
    }
	
	public void onResume(){
		super.onResume();
		queryParameters = queryParameters == null ? "?limit=1000" : queryParameters;
		sendFilteredRequest(rootResource + "/tangle/" + tangleId
 				+ "/request" + queryParameters);
	}
	
	/**
	 * Sets the listeners for the search button in the action bar.
	 * @author MohamedBassem
	 */
	private void setSearchListener() {
		final SearchView searchView = activity.getSearchView();
		
		searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {

			@Override
			public boolean onQueryTextSubmit(String query) {
				InputMethodManager inputManager = (InputMethodManager)getActivity().getSystemService(Context.INPUT_METHOD_SERVICE); 
				inputManager.hideSoftInputFromWindow(getActivity().getCurrentFocus().getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
				queryParameters = "?limit=1000&query="+query;
				sendFilteredRequest(rootResource + "/tangle/" + tangleId
		 				+ "/request" + queryParameters);
				return true;
			}
			
			@Override
			public boolean onQueryTextChange(String newText) {
				if(newText.equals("")){
					queryParameters = "?limit=1000";
					sendFilteredRequest(rootResource + "/tangle/" + tangleId
			 				+ "/request" + queryParameters);
				}
				return true;
			}
		});
		
	}

	/**
	 * This method is used to set the layout of the stream dynamically according
	 * to response of the request
	 * 
	 * @param res
	 *            , is the response string of the stream request
	 */
	private void setTheLayout(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray requestArray = response.getJSONArray("requests");
				if (count > 0 && requestArray != null) {
					LinearLayout layout = (LinearLayout) activity.findViewById(R.id.streamLayout);
					layout.removeAllViews();
					for (int i = 0; i < count && i < requestArray.length(); i++) {
						JSONObject request = requestArray.getJSONObject(i);
						if (request != null) {
							addRequest(request);
						}
					}
				} else {
					Toast.makeText(
							activity.getBaseContext(),
							"Sorry, There is no requests with the specified options",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to add specific request which is
	 * StreamRequestFragment to the layout of the stream
	 * 
	 * @param request
	 *            , is the request to be added in the layout
	 */
	private void addRequest(JSONObject request) {
		try {
			int userId = request.getInt("userId");
			String requesterName = request.getString("username");
			int requestId = request.getInt("id");
			String requestBody = request.getString("description");
			String requestOffersCount = "" + request.getInt("offersCount");
			String requesterButtonText = requesterName;
			String requestButtonText = requestBody;
			String requestPrice = "0";
					
			if(request.get("price") != null)
				requestPrice = "" + request.getInt("price");
			
			transaction = getFragmentManager().beginTransaction();
			StreamRequestFragment requestFragment = StreamRequestFragment
					.createInstance(requestId, userId, requestButtonText,
							requesterButtonText, requestPrice, requestOffersCount, this);
			transaction.add(R.id.streamLayout, requestFragment);
			transaction.commit();
		} catch (JSONException e) { 
			e.printStackTrace();
		}
	}

	/**
	 * This is a getter method used to get the tangle id
	 * 
	 * @return tangle id
	 */
	public int getTangleId() {
		return tangleId;
	}

	
	/**
	 * This is a getter method used to get the hashMap that maps a tag to its id
	 * 
	 * @return tagToId hashMap
	 */
	public HashMap<String, Integer> getTagToIdHashMap() {
		return tagToId;
	}

	/**
	 * This is a getter method used to get the hashMap that maps a user to its
	 * id
	 * 
	 * @return userToId hashMap
	 */
	public HashMap<String, Integer> getUserToIdHashMap() {
		return userToId;
	}

	

	/**
	 * This method is used to send a get request to get the stream filtered/not
	 * 
	 * @param url
	 *            , is the URL to which the request is going to be sent
	 */
	public void sendFilteredRequest(final String url) {
		sessionId = activity.getSharedPreferences(Config.SETTING, 0).getString(Config.SESSION_ID, "");
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					LinearLayout layout = (LinearLayout) activity.findViewById(R.id.streamLayout);
					layout.removeAllViews();
					setTheLayout(res);
				} else {
					Toast.makeText(activity.getBaseContext(),
							"Sorry, There is a problem in loading the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getStream.addHeader("X-SESSION-ID", getSessionId());
		getStream.execute();
	}
	
	@Override
	public void onAttach(Activity activity) {
		
		this.activity = (HomeActivity) activity;
		super.onAttach(activity);
	}
	
	public String getTangleName() {
		return tangleName;
	}

	/**
	 * This is a getter method used to get the session id of the user
	 * 
	 * @return session id
	 */
	public String getSessionId() {
		return sessionId;
	}
	
}
