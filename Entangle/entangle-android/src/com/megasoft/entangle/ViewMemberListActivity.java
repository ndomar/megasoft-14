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
		setAttributes();
		sendMemberListRequest(tangleId);

		ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
				android.R.layout.simple_list_item_1, values);
		setListAdapter(adapter);
	}

	public void sendMemberListRequest(int tangleID) {
		GetRequest getMemberListRequest = new GetRequest(
				"http://entangle2.apiary-mock.com/tangle/" + tangleID + "/user") {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					getMembers(res);

				} else {
					Toast.makeText(
							getBaseContext(),
							"Sorry, There is a problem in loading the member list",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getMemberListRequest.addHeader("X-SESSION-ID", getSessionId());
		getMemberListRequest.execute();
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
			JSONObject json = new JSONObject(res);
			int count = json.getInt("count");
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

	private void getNames(){
		
		for(int i=0;i<members.length;i++){
			try {
				values[i]= members[i].getString("username");
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
