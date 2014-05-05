package com.megasoft.entangle.megafragments;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.FilteringFragment;
import com.megasoft.entangle.R;
import com.megasoft.entangle.StreamRequestFragment;
import com.megasoft.requests.GetRequest;

public class TangleFragment extends Fragment {

	private FragmentActivity activity;
	private View view;
	private TextView tangle;
	
	/**
	 * The Intent used to redirect to other activities
	 */
	private Intent intent;

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
         
         ImageView filterButton = (ImageView) view.findViewById(R.id.filterButton);
         filterButton.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View arg0) {
				filterStream(arg0);
				
			}
		});
         
         tangleId = getArguments().getInt("tangleId");
         tangleName = getArguments().getString("tangleName");
         tangle = (TextView) view.findViewById(R.id.tangleName);
        
         sendFilteredRequest(rootResource + "/tangle/" + tangleId
 				+ "/request");
        Log.e("test", tangleId+"");
        Log.e("test", sessionId+"");
 		setRedirections();
 		tangle.setText(tangleName);
        return view;
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
	@SuppressLint("NewApi")
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
							requesterButtonText, requestPrice, requestOffersCount, getTangleId(), getTangleName());
			transaction.add(R.id.streamLayout, requestFragment);
			transaction.commit();
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to set the actions of the fixed buttons in the footer
	 * (stream, members, profile, invite) buttons
	 */
	private void setRedirections() {
		Button invite = (Button) view.findViewById(R.id.invite);
		setButtonRedirection(invite, "InviteUserActivity");
	}

	/**
	 * This method is a generic one that is called to set the action of a
	 * specific button to redirect upon clicking to a specific activity
	 * 
	 * @param button
	 *            , is the button intended to set its action
	 * @param activityName
	 *            , is the name of the class/activity to be redirected to upon
	 *            clicking the button
	 */
	private void setButtonRedirection(Button button, final String activityName) {
		button.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				try {
					if (activityName != null) {
						intent = new Intent(activity.getBaseContext(), Class
								.forName("com.megasoft.entangle."
										+ activityName));
						intent.putExtra("tangleId", getTangleId());
						intent.putExtra("tangleName", getTangleName());
						startActivity(intent);
					}
				} catch (ClassNotFoundException e) {
					Toast.makeText(
							((View) v.getParent()).getContext(),
							"Sorry, There is a problem in getting the "
									+ activityName + " page", Toast.LENGTH_LONG)
							.show();
				}
			}
		});
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
					Log.e("test",this.getErrorMessage());
					Log.e("test",url);
					Toast.makeText(activity.getBaseContext(),
							"Sorry, There is a problem in loading the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getStream.addHeader("X-SESSION-ID", getSessionId());
		getStream.execute();
	}

	/**
	 * This method is used to send a request to get all users/tags
	 * 
	 * @param url
	 *            , is the URL to which the request is going to be sent
	 * 
	 * @param type
	 *            , is an integer that indicates whether it is a getAllUsers
	 *            request or it a getAllTags request, where (0) means tags and
	 *            (1) means users
	 */
	public void sendGetAllRequest(String url, final int type) {
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					if (type == 0)
						setTagsSuggestions(res);
					else
						setUsersSuggestions(res);
				} else {
					Toast.makeText(
							activity.getBaseContext(),
							"Sorry, There is a problem in filtering the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getStream.addHeader("x-session-id", getSessionId());
		getStream.execute();
	}

	/**
	 * This method is used to handle the response of getting all tags, and sets
	 * the tagToId hashMap
	 * 
	 * @param res
	 *            , is the response of the request
	 */
	private void setTagsSuggestions(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray tagArray = response.getJSONArray("tags");
				if (count > 0 && tagArray != null) {
					tagToId = new HashMap<String, Integer>();
					for (int i = 0; i < count && i < tagArray.length(); i++) {
						JSONObject tag = tagArray.getJSONObject(i);
						if (tag != null) {
							String tagName = tag.getString("name");
							int tagId = tag.getInt("id");
							tagToId.put(tagName, tagId);
						}
					}

				} else {
					Toast.makeText(activity.getBaseContext(),
							"Sorry, There are no tags in this tangle",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to handle the response of getting all users, and sets
	 * the userToId hashMap, then it runs a FilteringFragment
	 * 
	 * @param res
	 *            , is the response of the request
	 */
	@SuppressLint("NewApi")
	private void setUsersSuggestions(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray usersArray = response.getJSONArray("users");
				if (count > 0 && usersArray != null) {
					userToId = new HashMap<String, Integer>();
					for (int i = 0; i < count && i < usersArray.length(); i++) {
						JSONObject user = usersArray.getJSONObject(i);
						if (user != null) {
							String userName = user.getString("username");
							int userId = user.getInt("id");
							userToId.put(userName, userId);
						}
					}
					FilteringFragment filter = FilteringFragment
							.createInstance(getTagToIdHashMap(),
									getUserToIdHashMap(), this);
					filter.show(activity.getFragmentManager(), "filter_dialog");
				} else {
					Toast.makeText(activity.getBaseContext(),
							"Sorry, There are no users in this tangle",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used when the filtering button is clicked, so it gets all
	 * users and all tags
	 * 
	 * @param view
	 *            , in this case it is the filtering button
	 */
	public void filterStream(View view) {
		String url = rootResource + "/tangle/" + getTangleId() + "/tag";
		sendGetAllRequest(url, 0);
		url = rootResource + "/tangle/" + getTangleId() + "/user";
		sendGetAllRequest(url, 1);
	}

	/**
	 * This method is used to return an ArrayList of all the tags came from the
	 * getAllTags request, this method is called from the filtering fragment to
	 * initialize its suggestions
	 * 
	 * @return ArrayList of tags
	 */
	public ArrayList<String> getTagsSuggestions() {
		HashMap<String, Integer> toId = getTagToIdHashMap();
		if (toId != null) {
			return new ArrayList<String>(toId.keySet());
		}
		return new ArrayList<String>();
	}

	/**
	 * This method is used to return an ArrayList of all the users came from the
	 * getAllUsers request, this method is called from the filtering fragment to
	 * initialize its suggestions
	 * 
	 * @return ArrayList of users
	 */
	public ArrayList<String> getUsersSuggestions() {
		HashMap<String, Integer> toId = getUserToIdHashMap();
		if (toId != null) {
			return new ArrayList<String>(toId.keySet());
		}
		return new ArrayList<String>();
	}
	
	@Override
	public void onAttach(Activity activity) {
		
		this.activity = (FragmentActivity) activity;
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
