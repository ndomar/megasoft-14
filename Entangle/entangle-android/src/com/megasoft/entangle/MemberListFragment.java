package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;

/*
 * A fragment for the member list.
 * @author Omar ElAzazy
 */
public class MemberListFragment extends Fragment {

	private int tangleId;
	private String sessionId;
	private String[] memberNames;
	private int[] memberBalances;
	private String[] memberAvatarURLs;
	private int[] memberIds;
	private LinearLayout memberListView;
	private int numberOfMembers;
	private ViewGroup container;
	
	public ViewGroup getContainer() {
		return container;
	}

	public void setContainer(ViewGroup container) {
		this.container = container;
	}

	public int getNumberOfMembers() {
		return numberOfMembers;
	}

	public void setNumberOfMembers(int numberOfMembers) {
		this.numberOfMembers = numberOfMembers;
	}

	public LinearLayout getMemberListView() {
		return memberListView;
	}

	public void setMemberListView(LinearLayout memberListView) {
		this.memberListView = memberListView;
	}

	public String[] getMemberNames() {
		return memberNames;
	}

	public void setMemberNames(String[] memberNames) {
		this.memberNames = memberNames;
	}

	public int[] getMemberBalances() {
		return memberBalances;
	}

	public void setMemberBalances(int[] memberBalances) {
		this.memberBalances = memberBalances;
	}

	public String[] getMemberAvatarURLs() {
		return memberAvatarURLs;
	}

	public void setMemberAvatarURLs(String[] memberAvatarURLs) {
		this.memberAvatarURLs = memberAvatarURLs;
	}

	public int[] getMemberIds() {
		return memberIds;
	}

	public void setMemberIds(int[] memberIds) {
		this.memberIds = memberIds;
	}

	public String getSessionId() {
		return sessionId;
	}

	public void setSessionId(String sessionId) {
		this.sessionId = sessionId;
	}
	
	public int getTangleId() {
		return tangleId;
	}
	
	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		setSessionId(getActivity().getSharedPreferences(Config.SETTING, 0).getString(Config.SESSION_ID, ""));
		setTangleId(getArguments().getInt(Config.TANGLE_ID));
		
		View view = inflater.inflate(R.layout.fragment_member_list,
				container, false);
		
		setContainer(container);
		setMemberListView((LinearLayout) view.findViewById(R.id.view_member_list));
		
		fetchMembers();
		
		return view;
	}
	
	/*
	 * It makes a request getting members and creating the fragments for member entries to be viewed
	 * @author Omar ElAzazy
	 */
	private void fetchMembers(){
		
		final AlertDialog ad = new AlertDialog.Builder(getActivity()).create();
		ad.setCancelable(false);
		ad.setMessage("Loading ...");
		ad.show();       

		
		GetRequest getRequest = new GetRequest(Config.API_BASE_URL
				+ "/tangle/" + getTangleId() + "/user") {
			public void onPostExecute(String response) {
				ad.dismiss();
				Log.e("test", this.getStatusCode() + ""); /////////////////////////////////
				
				if(!this.hasError() && this.getStatusCode() == 200){
					if(!showData(response)){
						toasterShow("Something went wrong, please try again later");
					}
				}else{
					toasterShow("Something went wrong, please try again later");
				}
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
	}
	
	
}