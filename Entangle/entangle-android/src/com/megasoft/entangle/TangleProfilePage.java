package com.megasoft.entangle;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Set;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.FragmentTransaction;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.requests.GetRequest;

/**
 * This class/activity is the one responsible for viewing the requests stream of
 * a certain tangle
 */
public class TangleProfilePage extends Activity {

	/**
	 * The Intent used to redirect to other activities
	 */
	private Intent intent;

	/**
	 * The domain to which the requests are sent
	 */
	private String rootResource = "http://entangle2.apiary.io/";

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

	private FragmentTransaction transaction;

	private HashMap<String, Integer> userToId = new HashMap<String, Integer>();

	private HashMap<String, Integer> tagToId = new HashMap<String, Integer>();

	// private ArrayList<String> hjfh = new ArrayList<String>()

	/**
	 * This method is called when the activity starts , it sets the attributes
	 * and redirections of all the views in this activity
	 * 
	 * @param savedInstanceState
	 *            , is the passed bundle from the previous activity
	 */
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_tangle_profile_page);
		setAttributes();
		sendStreamRequest();
		setRedirections();
	}

	/**
	 * This method is called to set the attributes of the activity passed from
	 * the other previous intent
	 */
	private void setAttributes() {
		if (getIntent() != null) {
			if (!getIntent().hasExtra("sessionId")) {
				intent = new Intent(this, MainActivity.class);
				// to be changed to login activity
			}
			if (!getIntent().hasExtra("tangleId")) {
				intent = new Intent(this, MainActivity.class);
				// to be changed to tangles' list activity
			}
			if (!getIntent().hasExtra("tangleName")) {
				intent = new Intent(this, MainActivity.class);
				// to be changed to tangles' list activity
			}
			tangleId = getIntent().getIntExtra("tangleId", 0);
			tangleName = getIntent().getStringExtra("tangleName");
			sessionId = getIntent().getStringExtra("sessionId");
			TextView tangle = (TextView) findViewById(R.id.tangleName);
			tangle.setText(tangleName);
		} else {
			intent = new Intent(this, MainActivity.class);
			// to be changed to login activity
		}
	}

	/**
	 * This method is used to send a request to get the requests stream without
	 * filtering only for the first time
	 */
	private void sendStreamRequest() {
		GetRequest getStream = new GetRequest(rootResource + "tangle/"
				+ getTangleId() + "/request") {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					setTheLayout(res);
				} else {
					Toast.makeText(getBaseContext(),
							"Sorry, There is a problem in loading the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getStream.addHeader("X-SESSION-ID", getSessionId());
		getStream.execute();
	}

	/**
	 * This method is used to set the layout of the stream dynamically according
	 * to response of the request
	 * 
	 * @param res
	 *            , is the response string of the stream request
	 */
	@SuppressLint("NewApi")
	private void setTheLayout(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray requestArray = response.getJSONArray("requests");
				if (count > 0 && requestArray != null) {
					LinearLayout layout = (LinearLayout) findViewById(R.id.streamLayout);
					layout.removeAllViews();
					for (int i = 0; i < count && i < requestArray.length(); i++) {
						JSONObject request = requestArray.getJSONObject(i);
						if (request != null) {
							addRequest(request);
						}
					}
				} else {
					Toast.makeText(
							getBaseContext(),
							"Sorry, There is no requests with the specified options",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to add specific request to the layout of the stream
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
			int requestOffersCount = request.getInt("offersCount");
			String requesterButtonText = "Requester : " + requesterName;
			String requestButtonText = "Request : " + requestBody
					+ "\nNumber of offers : " + requestOffersCount;
			transaction = getFragmentManager().beginTransaction();
			StreamRequestFragment requestFragment = StreamRequestFragment
					.createInstance(requestId, userId, requestButtonText,
							requesterButtonText);
			transaction.add(R.id.streamLayout, requestFragment);
			// transaction.addToBackStack(null);
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
		Button stream = (Button) findViewById(R.id.stream);
		setButtonRedirection(stream, "TangleProfilePage");

		Button members = (Button) findViewById(R.id.members);
		setButtonRedirection(members, "Members");

		Button profile = (Button) findViewById(R.id.profile);
		setButtonRedirection(profile, "Profile");

		Button invite = (Button) findViewById(R.id.invite);
		setButtonRedirection(invite, "Invite");
	}

	/**
	 * This method is called to set the action of a specific button to redirect
	 * upon clicking to a specific activity
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
						intent = new Intent(getBaseContext(), Class
								.forName("com.megasoft.entangle."
										+ activityName));
						intent.putExtra("tangleId", getTangleId());
						intent.putExtra("tangleName", getTangleName());
						intent.putExtra("sessionId", getSessionId());
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
	 * This is a getter method used to get the tangle name
	 * 
	 * @return tangle name
	 */
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

	public HashMap<String, Integer> getTagToIdHashMap() {
		return tagToId;
	}

	public HashMap<String, Integer> getUserToIdHashMap() {
		return userToId;
	}

	/**
	 * This method is used to set the options menu of the activity
	 */
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.view_stream, menu);
		return true;
	}

	/**
	 * This method is used to send a get request to get the stream filtered/not
	 * filtered in case of not the first request
	 * 
	 * @param url
	 *            , is the Url to which the request is going to be sent
	 */
	public void sendFilteredRequest(String url) {
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					LinearLayout layout = (LinearLayout) findViewById(R.id.streamLayout);
					layout.removeAllViews();
					setTheLayout(res);
				} else {
					Toast.makeText(getBaseContext(),
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
	 *            , is the Url to which the request is going to be sent
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
							getBaseContext(),
							"Sorry, There is a problem in filtering the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		if (type == 0) {
			getStream.addHeader("x-session-id", getSessionId());
		} else {
			getStream.addHeader("sessionid", getSessionId());
		}
		getStream.execute();
	}

	/**
	 * This method is used to handle the response of getting all tags
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
					Toast.makeText(getBaseContext(),
							"Sorry, There are no tags in this tangle",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to handle the response of getting all users
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
									getUserToIdHashMap());
					filter.show(getFragmentManager(), "filter_dialog");
				} else {
					Toast.makeText(getBaseContext(),
							"Sorry, There are no users in this tangle",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	public void filterStream(View view) {
		String url = rootResource + "tangle/" + getTangleId() + "/tag";
		sendGetAllRequest(url, 0);
		url = rootResource + "tangle/" + getTangleId() + "/user";
		sendGetAllRequest(url, 1);
	}

	public ArrayList<String> getTagsSuggestions() {
		HashMap<String, Integer> toId = getTagToIdHashMap();
		if (toId != null) {
			return new ArrayList<String>(toId.keySet());
		}
		return new ArrayList<String>();
	}

	public ArrayList<String> getUsersSuggestions() {
		HashMap<String, Integer> toId = getUserToIdHashMap();
		if (toId != null) {
			return new ArrayList<String>(toId.keySet());
		}
		return new ArrayList<String>();
	}
}
