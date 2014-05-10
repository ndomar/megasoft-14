package com.megasoft.entangle;

import org.json.JSONObject;

import android.R.color;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.TextView;

public class NotificationStreamFragment extends Fragment {
	
	private int userId;
	private View view;
	private int notificationId;
	private String notificationDescription;
	private String notificationType;
	private boolean seen;
	
	public void setData(int notificationId , int userId , boolean seen , String notificationDescription , 
				String notificationType) {
		
		this.userId = userId;
		this.notificationId = notificationId;
		this.notificationDescription = notificationDescription;
		this.notificationType = notificationType;
		this.seen = seen;
		
	}
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		view = inflater.inflate(R.layout.fragment_notification_stream,
				container, false);
		ImageButton mark = (ImageButton) view.findViewById(R.id.imageButton1);
		mark.setBackgroundColor(color.white);
		TextView notificationTypeView = (TextView) view.findViewById(R.id.notificationType);
		TextView notificationDescriptionView = (TextView) view.findViewById(R.id.notificationDescription);
		notificationTypeView.setTextSize(20);
		if(!this.seen) {
			notificationTypeView.setTypeface(null, Typeface.BOLD);
			notificationDescriptionView.setTypeface(null, Typeface.BOLD);
		} else {
			mark.setVisibility(mark.INVISIBLE);
			notificationTypeView.setTextColor(Color.GRAY);
			notificationDescriptionView.setTextColor(Color.GRAY);
		}
		notificationTypeView.setText(this.notificationType);
		notificationDescriptionView.setText(this.notificationDescription);
		
		return view;
	}
	
	public int getNotificationId() {
		return this.notificationId;
	}
	
	/**
	 * This method is used to set the notification status as seen
	 * @author Mohamed Ayman
	 */
	public void setSeen() {
		
		JSONObject json = new JSONObject();

		//PutRequest putRequest = new PutRequest("http://entangle2.apiary.io/notification/1/set-seen");
		//putRequest.addHeader("sessionID", "testest");
		//putRequest.setBody(json);
		//putRequest.execute("http://entangle2.apiary.io/1/notification/set-seen");
	}
	
	public void checkSeen() {
		
	}

}
