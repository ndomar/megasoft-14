package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

import android.os.Bundle;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.view.Gravity;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;

public class ViewStream extends Activity {
	private Intent intent;
	private String rootResource = "http://entangle2.apiary.io/";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_stream);
		sendStreamRequest();
		setRedirections();
	}

	private void sendStreamRequest() {
		// only changed for testing
		// int tangle = getTangleId();
		// String sessionId = getSessionId();
		int tangle = 1;
		String sessionId = "sadsadasda";
		GetRequest getStream = new GetRequest(rootResource + "tangle/" + tangle
				+ "/stream") {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					setTheLayout(res);
				}
				// what to be done if there was an error ?
			}
		};
		getStream.addHeader("X-SESSION-ID", sessionId);
		getStream.execute();
	}

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

				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	@SuppressLint("NewApi")
	private void addRequest(JSONObject request) {

		try {
			LayoutParams params = new LinearLayout.LayoutParams(
					LayoutParams.WRAP_CONTENT, LayoutParams.MATCH_PARENT);
			LayoutParams params1 = new LinearLayout.LayoutParams(
					LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT);
			LinearLayout layout = (LinearLayout) findViewById(R.id.l1);
			int userId = request.getInt("userId");
			String requesterName = request.getString("requesterName");
			int requestId = request.getInt("requestId");
			String requestBody = request.getString("requestBody");
			int requestOffersCount = request.getInt("requestOffersCount");
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
			// req.setPaddingRelative(5, 5, 5, 10);
			setRequestRedirection(req);
			req.setLayoutParams(params1);
			layout.addView(req);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	private int getTangleId() {
		SharedPreferences config = getSharedPreferences("AppConfig", 0);
		if (!config.contains("tangleId")) {
			startActivity(new Intent(this, MainActivity.class));
			// to be changed to the one after login
		}
		int tangleId = config.getInt("tangleId", (Integer) null);
		return tangleId;
	}

	private String getSessionId() {
		SharedPreferences config = getSharedPreferences("AppConfig", 0);
		if (!config.contains("X-SESSION-ID")) {
			startActivity(new Intent(this, MainActivity.class));
			// to be changed to the login activity
		}
		String sessionId = config.getString("X-SESSION-ID", null);
		return sessionId;
	}

	private void setRequesterRedirection(Button requester) {
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

	private void setRequestRedirection(Button request) {
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

	private void setRedirections() {
		Button stream = (Button) findViewById(R.id.stream);
		setStreamRedirection(stream);

		Button members = (Button) findViewById(R.id.members);
		setMembersRedirection(members);

		Button profile = (Button) findViewById(R.id.profile);
		setProfileRedirection(profile);

		Button invite = (Button) findViewById(R.id.invite);
		setInviteRedirection(invite);
	}

	private void setStreamRedirection(Button stream) {
		stream.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				intent = new Intent(getBaseContext(), ViewStream.class);
				startActivity(intent);
			}
		});
	}

	private void setMembersRedirection(Button members) {
		members.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				intent = new Intent(getBaseContext(), Members.class);
				startActivity(intent);
			}
		});
	}

	private void setProfileRedirection(Button profile) {
		profile.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				intent = new Intent(getBaseContext(), Profile.class);
				startActivity(intent);
			}
		});
	}

	private void setInviteRedirection(Button invite) {
		invite.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				intent = new Intent(getBaseContext(), Invite.class);
				startActivity(intent);
			}
		});
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.view_stream, menu);
		return true;

	}

}
