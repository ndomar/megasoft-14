package com.megasoft.entangle;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

import android.os.Bundle;
import android.app.Activity;
import android.app.ListActivity;
import android.content.Intent;
import android.view.Menu;
import android.widget.ArrayAdapter;
import android.widget.Toast;

public class ViewMemberListActivity extends ListActivity {

	private Intent intent;
	private String sessionId;
	private int tangleId;
	private JSONObject[] members;
	private String[] values;

	public void onCreate(Bundle bundle) {
		super.onCreate(bundle);
		setContentView(R.layout.activity_view_member_list);
		setAttributes();
		sendMemberListRequest(tangleId);

		System.out.println("WASALNA HENA");
		if (values[0] == null)
			System.out.println("NULL ---");

		final ArrayList<String> list = new ArrayList<String>();
		for (int i = 0; i < values.length; ++i) {
			list.add(values[i]);
		}

		ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
				R.layout.activity_view_member_list, list);
		System.out.println("weselna hena 2222222");
		setListAdapter(adapter);
	}

	public void sendMemberListRequest(int tangleID) {
		System.out.println("DAKHAAAAAALT");
		GetRequest getMemberListRequest = new GetRequest(
				"http://entangle2.apiary-mock.com/tangle/" + tangleID + "/user") {
			protected void onPostExecute(String res) {
				System.out.println("GAH RESPONSE");
				System.out.println(this.hasError() + " HHHHHHHHHHHHHHHHHHHHHH "
						+ res);
				if (!this.hasError() && res != null) {
					getMembers(res);
					getNames();

				} else {
					Toast.makeText(
							getBaseContext(),
							"Sorry, There is a problem in loading the member list",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		System.out.println("GETTING SESSION ID " + sessionId);
		System.out.println(getSessionId());
		getMemberListRequest.addHeader("X-SESSION-ID", getSessionId());
		System.out.println("FDSJGFKDGJDSLGFJSKLGJKLJGLKFDJLGKJDFs");
		getMemberListRequest.execute();
		System.out.println("JKFJDSLJFKLSAJFWAIFJEWAWJ");
	}

	private void setAttributes() {
		if (getIntent() != null) {
			tangleId = getIntent().getIntExtra("tangleId", 0);
			sessionId = getIntent().getStringExtra("sessionId");
		}
	}

	private String getSessionId() {
		// TODO Auto-generated method stub
		return sessionId;
	}

	private void getMembers(String res) {
		try {
			System.out.println(res);
			JSONObject json = new JSONObject(res);
			if (json == null)
				System.out.println("NULL -1");
			int count = json.getInt("count");
			System.out.println("COUNT: " + count);
			members = new JSONObject[count];
			values = new String[count];
			JSONArray jsonArray = json.getJSONArray("users");
			for (int i = 0; i < jsonArray.length(); i++) {
				members[i] = jsonArray.getJSONObject(i);
			}

		} catch (JSONException e) {
			e.printStackTrace();
		}

	}

	private void getNames() {
		if (members == null)
			System.out.println("NULL 1");
		for (int i = 0; i < members.length; i++) {
			try {
				if (members[i] == null)
					System.out.println("NULL 2");
				if (values == null)
					System.out.println("NULL 3");
				if (values[i] == null)
					System.out.println("NULL 4");
				values[i] = members[i].getString("username");
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
