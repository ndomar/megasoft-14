package com.megasoft.entangle;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.requests.GetRequest;

import android.os.Bundle;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.Menu;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

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

	/**
	 * The EditText used for full text search
	 */
	private EditText text;

	/**
	 * The drop down list used to choose the type of filtering
	 */
	private Spinner spinner1;

	/**
	 * The drop down list used to choose the tag/user to filter the stream with
	 */
	private Spinner spinner2;

	/**
	 * The adapter used to set the data of the second drop down list
	 */
	private ArrayAdapter<String> dataAdapter;

	/**
	 * This hashMap is used to map user/tag to its id
	 */
	private HashMap<String, Integer> idHashMap;

	/**
	 * This int is used to differentiate whether it tag or user filtering
	 */
	private int type;

	/**
	 * This is used to set the behavior of the EditText used in full text search
	 */
	private TextWatcher watcher = new TextWatcher() {
		@Override
		public void onTextChanged(CharSequence arg0, int arg1, int arg2,
				int arg3) {
		}

		@Override
		public void beforeTextChanged(CharSequence arg0, int arg1, int arg2,
				int arg3) {
		}

		/**
		 * This method is used to send a request to filter with full text search
		 * given the text
		 */
		@Override
		public void afterTextChanged(Editable arg0) {
			String fullText = text.getText().toString();
			if (fullText != null
					&& spinner1.getSelectedItem() != null
					&& spinner1.getSelectedItem().toString() != null
					&& spinner1.getSelectedItem().toString()
							.equals("Full Text Search")) {
				sendFilteredRequest(rootResource + "tangle/" + getTangleId()
						+ "/request?fulltext="
						+ (fullText.trim().replace(' ', '+')));
			}
		}
	};

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
		setAttributes(savedInstanceState);
		setContentView(R.layout.activity_tangle_profile_page);
		sendStreamRequest();
		setRedirections();
		addListenerOnSpinnerItemSelection();
		setEditableView();
	}

	/**
	 * This method is called is called to set the attributes of the activity
	 * 
	 * @param savedInstanceState
	 *            , is the saved bundle of the activity
	 */
	@SuppressLint("NewApi")
	private void setAttributes(Bundle savedInstanceState) {
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
			// to be changed if the API is less than 12
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
	 * This method is used to add a listener to the EditText used in filtering
	 * with full text search
	 */
	private void setEditableView() {
		text = (EditText) findViewById(R.id.text);
		text.addTextChangedListener(watcher);
	}

	/**
	 * This method is called to set a listener to the filtering options drop
	 * down list
	 */
	private void addListenerOnSpinnerItemSelection() {

		spinner1 = (Spinner) findViewById(R.id.filterSpinner);
		spinner2 = (Spinner) findViewById(R.id.choiceSpinner);
		if (spinner1 != null) {
			spinner1.setOnItemSelectedListener(new SpinnerListener1());
		}
		if (spinner2 != null) {
			spinner2.setOnItemSelectedListener(new SpinnerListener2());
		}
	}

	/**
	 * This method is used to send a request to get the requests stream without
	 * filtering
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
		getStream.addHeader("x-session-id", getSessionId());
		getStream.execute();
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
			LayoutParams params = new LinearLayout.LayoutParams(
					LayoutParams.WRAP_CONTENT, LayoutParams.MATCH_PARENT);
			LayoutParams params1 = new LinearLayout.LayoutParams(
					LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT);
			LinearLayout layout = (LinearLayout) findViewById(R.id.l1);
			int userId = request.getInt("userId");
			String requesterName = request.getString("username");
			int requestId = request.getInt("id");
			String requestBody = request.getString("description");
			int requestOffersCount = request.getInt("offersCount");
			Button requester = new Button(this);
			requester.setId(userId);
			requester.setText("Requester : " + requesterName);
			setRequesterRedirection(requester);
			requester.setGravity(Gravity.CENTER_HORIZONTAL);
			requester.setLayoutParams(params);
			layout.addView(requester);
			Button req = new Button(this);
			req.setId(requestId);
			req.setText("Request : " + requestBody + "\n"
					+ "Number of offers : " + requestOffersCount);
			setRequestRedirection(req);
			req.setLayoutParams(params1);
			layout.addView(req);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to set the action of the requester button
	 * 
	 * @param requester
	 *            , is the requester button
	 */
	private void setRequesterRedirection(Button requester) {
		requester.setTextSize(16);
		requester.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				int tmpId = ((Button) v).getId();
				Intent intent2 = new Intent(getBaseContext(), Profile.class);
				intent2.putExtra("tangleId", getTangleId());
				intent2.putExtra("tangleName", getTangleName());
				intent2.putExtra("sessionId", getSessionId());
				intent2.putExtra("requesterId", tmpId);
				startActivity(intent2);
			}
		});
	}

	/**
	 * This method is used to set the action of the request button
	 * 
	 * @param request
	 *            , is the request button
	 */
	private void setRequestRedirection(Button request) {
		request.setTextSize(16);
		request.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				int tmpId = ((Button) v).getId();
				Intent intent2 = new Intent(getBaseContext(), RequestPage.class);
				intent2.putExtra("tangleId", getTangleId());
				intent2.putExtra("tangleName", getTangleName());
				intent2.putExtra("sessionId", getSessionId());
				intent2.putExtra("requestId", tmpId);
				startActivity(intent2);
			}
		});
	}

	/**
	 * This method is used to set the actions of the fixed buttons (stream,
	 * members, profile, invite)
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
	 * This method is called to set the action of a specific button
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
	 * filtered
	 * 
	 * @param url
	 *            , is the Url to which the request is going to be sent
	 */
	public void sendFilteredRequest(String url) {
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					LinearLayout layout = (LinearLayout) findViewById(R.id.l1);
					layout.removeAllViews();
					setTheLayout(res);
				} else {
					Toast.makeText(getBaseContext(),
							"Sorry, There is a problem in loading the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getStream.addHeader("x-session-id", getSessionId());
		getStream.execute();
	}

	/**
	 * This method is used to send a request to get all users/tags
	 * 
	 * @param url
	 *            , , is the Url to which the request is going to be sent
	 */
	public void sendGetAllRequest(String url) {
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					if (type == 0)
						setSpinnerTag(res);
					else
						setSpinnerRequester(res);
				} else {
					Toast.makeText(getBaseContext(),
							"Sorry, There is a problem in loading the stream",
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
	private void setSpinnerTag(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray tagArray = response.getJSONArray("tags");
				if (count > 0 && tagArray != null) {
					idHashMap = new HashMap<String, Integer>();
					ArrayList<String> list = new ArrayList<String>();
					list.add("Please choose a tag");
					for (int i = 0; i < count && i < tagArray.length(); i++) {
						JSONObject tag = tagArray.getJSONObject(i);
						if (tag != null) {
							String tagName = tag.getString("name");
							int tagId = tag.getInt("id");
							idHashMap.put(tagName, tagId);
							list.add(tagName);
						}
					}
					setAdapterData(list);
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
	private void setSpinnerRequester(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray usersArray = response.getJSONArray("users");
				if (count > 0 && usersArray != null) {
					idHashMap = new HashMap<String, Integer>();
					ArrayList<String> list = new ArrayList<String>();
					list.add("Please choose a user");
					for (int i = 0; i < count && i < usersArray.length(); i++) {
						JSONObject user = usersArray.getJSONObject(i);
						if (user != null) {
							String userName = user.getString("userName");
							int userId = user.getInt("userId");
							idHashMap.put(userName, userId);
							list.add(userName);
						}
					}
					setAdapterData(list);
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

	/**
	 * This method is used to set the data of the second drop down list
	 * (tags/users)
	 * 
	 * @param list
	 *            , is the data to be put in the drop down list
	 */
	private void setAdapterData(ArrayList<String> list) {
		dataAdapter = new ArrayAdapter<String>(this,
				android.R.layout.simple_spinner_item, list);
		dataAdapter
				.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		spinner2.setAdapter(dataAdapter);
		text.setLayoutParams(new LinearLayout.LayoutParams(
				LayoutParams.MATCH_PARENT, 0));
		spinner2.setVisibility(0);
		spinner2.setLayoutParams(new LinearLayout.LayoutParams(
				LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
	}

	/**
	 * This class is used to customize the action done when an element in drop
	 * down list of the filtering options is chosen
	 */
	class SpinnerListener1 implements OnItemSelectedListener {

		/**
		 * This method is used to override the behavior of the drop down list
		 * when selecting an element
		 */
		@Override
		public void onItemSelected(AdapterView<?> parent, View view, int pos,
				long id) {
			Toast.makeText(
					parent.getContext(),
					"You choosed to filter with : "
							+ parent.getItemAtPosition(pos).toString(),
					Toast.LENGTH_SHORT).show();
			if (parent.getSelectedItem() != null
					&& parent.getSelectedItem().toString() != null) {
				String selection = parent.getSelectedItem().toString();
				if (selection.equals("Full Text Search")) {
					caseFullText();
					caseFullTextOrNone();
				} else {
					String url = rootResource;
					disablingTextEditor();
					if (selection.equals("None")) {
						caseFullTextOrNone();
						url += "tangle/" + getTangleId() + "/request";
						sendFilteredRequest(url);
					} else if (selection.equals("Tag")) {
						url += "tangle/" + getTangleId() + "/tag";
						type = 0;
						sendGetAllRequest(url);
					} else if (selection.equals("Requester Name")) {
						url += "tangle/" + getTangleId() + "/users";
						type = 1;
						sendGetAllRequest(url);
					}
				}
			}
		}

		/**
		 * This method is to make the second drop down list invisible and set
		 * the EditText to be visible
		 */
		private void caseFullTextOrNone() {
			spinner2.setLayoutParams(new LinearLayout.LayoutParams(
					LayoutParams.MATCH_PARENT, 0));
			text.setVisibility(0);
			text.setLayoutParams(new LinearLayout.LayoutParams(
					LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
		}

		/**
		 * This method is used to make the EditText enabled
		 */
		private void caseFullText() {
			text.setEnabled(true);
			text.setHint("Write a Full Text to filter with");
		}

		/**
		 * This method is used to set the EditText disabled
		 */
		private void disablingTextEditor() {
			text.setText("");
			text.setEnabled(false);
			text.setHint("Choose Full Text Search to be able to write :)");
		}

		@Override
		public void onNothingSelected(AdapterView<?> arg0) {
			// TODO Auto-generated method stub
		}

	}

	/**
	 * This class is used to customize the action done when an element in the
	 * second drop down list (tags/users) is chosen
	 */
	class SpinnerListener2 implements OnItemSelectedListener {

		/**
		 * This method is used to override the behavior of the second drop down
		 * list when selecting an item from it
		 */
		@Override
		public void onItemSelected(AdapterView<?> parent, View view, int pos,
				long id) {
			if (parent.getSelectedItem() != null
					&& parent.getSelectedItem().toString() != null) {
				String selection = parent.getSelectedItem().toString();
				if (!selection.startsWith("Please choose a")) {
					String url = rootResource;
					if (idHashMap != null) {
						int keyId = idHashMap.get(selection);
						if (type == 0) {
							url += "/tangle/" + getTangleId()
									+ "/request?tagid=" + keyId;
						} else if (type == 1) {
							url += "/tangle/" + getTangleId()
									+ "/request?userid=" + keyId;
						}
						sendFilteredRequest(url);
					}
				}
			}
		}

		@Override
		public void onNothingSelected(AdapterView<?> arg0) {
			// TODO Auto-generated method stub
		}
	}
}
