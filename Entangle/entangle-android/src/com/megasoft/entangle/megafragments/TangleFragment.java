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
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.HomeActivity;
import com.megasoft.entangle.R;
import com.megasoft.entangle.StreamRequestFragment;
import com.megasoft.requests.GetRequest;

public class TangleFragment extends Fragment {

	private HomeActivity activity;
	private View view;
	private TextView loadMoreTrigger; 
	
	private String lastDate;
	
	/**
	 * Default number of requests to be loaded every time.
	 */
	private int defaultRequestLimit = 5;
	
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
	 * Last query cached.
	 */
	private String lastQuery;

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

         loadMoreTrigger = (TextView) view.findViewById(R.id.load_more);
         loadMoreTrigger.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				loadMoreTrigger.setText(getResources().getString(R.string.loading));
				sendFilteredRequest(rootResource + "/tangle/" + tangleId
		 				+ "/request", true, null);	
			}
		});
         
        tangleId = getArguments().getInt("tangleId");
        tangleName = getArguments().getString("tangleName");
 		setSearchListener();
 		
        return view;
    }
	
	public void onResume(){
		super.onResume();
		sendFilteredRequest(rootResource + "/tangle/" + tangleId
	 				+ "/request", false, null);
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
				sendFilteredRequest(rootResource + "/tangle/" + tangleId
		 				+ "/request", false, query);

				return true;
			}
			
			@Override
			public boolean onQueryTextChange(String newText) {
				if(newText.equals("")){
					sendFilteredRequest(rootResource + "/tangle/" + tangleId
			 				+ "/request", false, null);
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
	private void setTheLayout(String res, boolean isLoadMore) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray requestArray = response.getJSONArray("requests");
				if (count > 0 && requestArray != null) {
					LinearLayout layout = (LinearLayout) activity.findViewById(R.id.streamLayout);
					if (!isLoadMore) {
						layout.removeAllViews();
					}
					for (int i = 0; i < count && i < requestArray.length(); i++) {
						JSONObject request = requestArray.getJSONObject(i);
						if (request != null) {
							addRequest(request);
						}
					}
				} else {
					Toast.makeText(
							activity.getBaseContext(),
							getResources().getString(R.string.no_more_requests),
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
			int userId 					= request.getInt("userId");
			String requesterName 		= request.getString("username");
			int requestId 				= request.getInt("id");
			String requestBody 			= request.getString("description");
			String requestOffersCount 	= "" + request.getInt("offersCount");
			String requesterButtonText 	= requesterName;
			String requestButtonText 	= requestBody;
			String requestPrice 		= "0";
			this.lastDate 				= request.getString("date");
					
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
	 * @param url, isLoadMore
	 *            is the URL to which the request is going to be sent
	 *            isLoad more flag is set to true if more older requests to be fetced (requests after lastDate)
	 */
	public void sendFilteredRequest(String url, final boolean isLoadMore, String query) {
		if (query != null) {
			this.lastQuery = query;
		}
		sessionId = activity.getSharedPreferences(Config.SETTING, 0).getString(Config.SESSION_ID, "");
		url += "?limit=" + defaultRequestLimit;
		if (isLoadMore) {
			query = lastQuery;
			url += "&lastDate=" + lastDate.replace(" ", "$20");
		}
		if (query != null) {
			url += "&query=" + query;
		}
		final String finalUrl = url;
		GetRequest getStream = new GetRequest(finalUrl) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					LinearLayout layout = (LinearLayout) activity.findViewById(R.id.streamLayout);
					if (!isLoadMore) {
						layout.removeAllViews();
					}
					setTheLayout(res, isLoadMore);
				} else {
					Toast.makeText(activity.getBaseContext(),
							"Sorry, There is a problem in loading the stream",
							Toast.LENGTH_LONG).show();
				}
				// last date indicates that the steram is not empty
				if (lastDate != null) {
					loadMoreTrigger.setText(getResources().getString(R.string.load_more));
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
