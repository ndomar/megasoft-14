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
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

public class ViewMemberListActivity extends ListActivity implements
		OnItemClickListener {

	private Intent intent;
	private String sessionId;
	private int tangleId;
	private JSONObject[] members;
	private String[] names;
	ListView listView;
	private int[] userId;
	private String[] iconUrl;
	private int[] userBalance;

	public void onCreate(Bundle bundle) {
		super.onCreate(bundle);
		setContentView(R.layout.activity_view_member_list);
		setAttributes();
		System.out.println(tangleId);
		sendMemberListRequest(tangleId);

		System.out.println("WASALNA HENA");

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
					getData();
					displayNames();
				} else {
					Toast.makeText(
							getBaseContext(),
							"Sorry, There is a problem in loading the member list",
							Toast.LENGTH_LONG).show();
				}
			}

		};
		System.out.println("GETTING SESSION ID " + sessionId);
		// System.out.println(getSessionId());
		getMemberListRequest.addHeader("X-SESSION-ID", getSessionId());
		// System.out.println("FDSJGFKDGJDSLGFJSKLGJKLJGLKFDJLGKJDFs");
		getMemberListRequest.execute();
		// System.out.println("JKFJDSLJFKLSAJFWAIFJEWAWJ");
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
			System.out.println("Inside getmembers");
			JSONObject json = new JSONObject(res);
			System.out.println("JSON Done!");
			int count = json.getInt("count");
			System.out.println("COUNT: " + count);
			members = new JSONObject[count];
			names = new String[count];
			JSONArray jsonArray = json.getJSONArray("users");
			for (int i = 0; i < jsonArray.length(); i++) {
				members[i] = jsonArray.getJSONObject(i);
			}

		} catch (JSONException e) {
			e.printStackTrace();
		}

	}

	private void displayNames() {
		listView = getListView();
		listView.setAdapter(new ArrayAdapter<String>(this,
				android.R.layout.simple_list_item_1, android.R.id.text1, names));
		listView.setOnItemClickListener(this);

	}

	public void onItemClick(AdapterView<?> adapter, View view, int position,
			long id) {
		System.out.println("position: " + position);
		System.out.println("ID: " + id);
		System.out.println();
		Toast.makeText(getApplicationContext(), ((TextView) view).getText(),
				Toast.LENGTH_SHORT).show();

	}

	private void getData() {
		if (members == null)
			System.out.println("NULL 1");
		for (int i = 0; i < members.length; i++) {
			try {
				iconUrl[i]= members[i].getString("iconUrl");
				userId[i]= members[i].getInt("id");
				names[i] = members[i].getString("username");
				userBalance[i] = members[i].getInt("balance");
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
	}
}
