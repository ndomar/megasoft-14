package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

import android.os.Bundle;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.view.Gravity;
import android.view.Menu;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

/**
 * This class/activity is the one responsible for viewing the requests stream of
 * a certain tangle
 */
public class ViewStream extends Activity {

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
		setContentView(R.layout.activity_view_stream);
		sendStreamRequest();
		setRedirections();
		addListenerOnSpinnerItemSelection();
		// setEditableViews();
	}

	/**
	 * This method is called is called to set the attributes of the activity
	 * 
	 * @param savedInstanceState
	 *            , is the passed bundle from the previous activity
	 */
	@SuppressLint("NewApi")
	private void setAttributes(Bundle savedInstanceState) {
		if (savedInstanceState != null) {
			if (!savedInstanceState.containsKey("sessionId")) {
				intent = new Intent(this, MainActivity.class);
				// to be changed to login activity
			}
			if (!savedInstanceState.containsKey("tangleId")) {
				intent = new Intent(this, MainActivity.class);
				// to be changed to tangles' list activity
			}
			if (!savedInstanceState.containsKey("tangleName")) {
				intent = new Intent(this, MainActivity.class);
				// to be changed to tangles' list activity
			}
			tangleId = savedInstanceState.getInt("tangleId", 0);
			// to be changed if the API is less than 12
			tangleName = savedInstanceState.getString("tangleName",
					"TangleName");
			sessionId = savedInstanceState
					.getString("sessionId", "NoSessionId");
			TextView tangle = (TextView) findViewById(R.id.tangleName);
			tangle.setText(tangleName);
		} else {
			intent = new Intent(this, MainActivity.class);
			// to be changed to login activity
		}
	}

	// private void setEditableViews() {
	// EditText tag = (EditText)findViewById(R.id.tag);
	// EditText user = (EditText)findViewById(R.id.user);
	// EditText fullText = (EditText)findViewById(R.id.text);
	// }

	/**
	 * This method is called to set a listener to the filtering options drop
	 * down list
	 */
	private void addListenerOnSpinnerItemSelection() {

		Spinner spinner1 = (Spinner) findViewById(R.id.filterSpinner);
		spinner1.setOnItemSelectedListener(new SpinnerListener());

	}

	/**
	 * This method is used to send a request to get the requests stream without
	 * filtering
	 */
	private void sendStreamRequest() {
		GetRequest getStream = new GetRequest(rootResource + "tangle/"
				+ tangleId + "/request") {
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
		getStream.addHeader("X-SESSION-ID", sessionId);
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
			System.out.println(res);
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
				Intent intent2 = new Intent(getBaseContext(),
						RequestInformation.class);
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
		setButtonRedirection(stream, "ViewStream");

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

}

/**
 * This class is used to customize the action done when an element in drop down
 * list of the filtering options is chosen
 */
class SpinnerListener implements OnItemSelectedListener {

	/**
	 * This method is used to override the behavior of the drop down list when
	 * selecting an element
	 */
	@Override
	public void onItemSelected(AdapterView<?> parent, View view, int pos,
			long id) {
		Toast.makeText(
				parent.getContext(),
				"OnItemSelectedListener : "
						+ parent.getItemAtPosition(pos).toString(),
				Toast.LENGTH_SHORT).show();
	}

	@Override
	public void onNothingSelected(AdapterView<?> arg0) {
		// TODO Auto-generated method stub
	}

}